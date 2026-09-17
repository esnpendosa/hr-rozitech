<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Services\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Login user and issue Sanctum token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $key = 'login:' . $request->ip();

        // Rate limit: 5 attempts per 5 minutes
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return ApiResponse::error(
                __('auth.throttle', ['seconds' => $seconds]),
                status: 429
            );
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 300); // decay 5 minutes

            // Audit failed login
            AuditLog::create([
                'tenant_id'     => null,
                'actor_user_id' => $user?->id,
                'action'        => 'auth.login_failed',
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
                'new_values'    => ['email' => $request->email],
            ]);

            return ApiResponse::error(__('auth.failed'), status: 401);
        }

        RateLimiter::clear($key);

        $deviceName = $request->device_name ?? ($request->userAgent() ?? 'Unknown Device');
        $token      = $user->createToken($deviceName)->plainTextToken;
        $tokenId    = Str::before($token, '|');

        // Record session
        UserSession::create([
            'user_id'        => $user->id,
            'tenant_id'      => $user->tenant_id,
            'token_id'       => $tokenId,
            'device_name'    => $deviceName,
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
            'last_active_at' => now(),
        ]);

        // Audit successful login
        AuditLog::create([
            'tenant_id'     => $user->tenant_id,
            'actor_user_id' => $user->id,
            'action'        => 'auth.login',
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
        ]);

        return ApiResponse::success([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $this->userResource($user),
        ], __('auth.login_success'));
    }

    /**
     * Logout: revoke current token and remove session record.
     */
    public function logout(Request $request): JsonResponse
    {
        $user    = $request->user();
        $tokenId = $user->currentAccessToken()?->id;

        // Revoke current token
        $user->currentAccessToken()?->delete();

        // Remove session record
        if ($tokenId) {
            UserSession::where('token_id', (string) $tokenId)
                ->where('user_id', $user->id)
                ->delete();
        }

        AuditLog::create([
            'tenant_id'     => $user->tenant_id,
            'actor_user_id' => $user->id,
            'action'        => 'auth.logout',
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'request_id'    => $request->header('X-Request-ID', (string) Str::uuid()),
        ]);

        return ApiResponse::success(null, __('auth.logout_success'));
    }

    /**
     * Return currently authenticated user with roles.
     */
    public function me(Request $request): JsonResponse
    {
        $user    = $request->user()->load('roles');
        $tokenId = $user->currentAccessToken()?->id;

        // Update last active
        if ($tokenId) {
            UserSession::where('token_id', (string) $tokenId)
                ->where('user_id', $user->id)
                ->update(['last_active_at' => now()]);
        }

        return ApiResponse::success($this->userResource($user));
    }

    /**
     * Refresh: revoke old token, issue new token, update session.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user       = $request->user();
        $oldToken   = $user->currentAccessToken();
        $deviceName = $oldToken?->name ?? 'Unknown Device';

        // Revoke old token
        $oldToken?->delete();

        // Create new token
        $token      = $user->createToken($deviceName)->plainTextToken;
        $newTokenId = Str::before($token, '|');

        // Update session record
        UserSession::where('token_id', (string) ($oldToken?->id ?? ''))
            ->where('user_id', $user->id)
            ->update([
                'token_id'       => $newTokenId,
                'last_active_at' => now(),
            ]);

        return ApiResponse::success([
            'token'      => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Register / update FCM push-notification token.
     */
    public function registerFcmToken(Request $request): JsonResponse
    {
        $request->validate(['fcm_token' => ['required', 'string']]);

        $user = $request->user();
        $user->update(['fcm_token' => $request->fcm_token]);

        $tokenId = $user->currentAccessToken()?->id;
        if ($tokenId) {
            UserSession::where('token_id', (string) $tokenId)
                ->where('user_id', $user->id)
                ->update(['fcm_token' => $request->fcm_token]);
        }

        return ApiResponse::success(null, __('auth.fcm_token_registered'));
    }

    /**
     * List all active sessions for the authenticated user.
     */
    public function sessions(Request $request): JsonResponse
    {
        $user     = $request->user();
        $sessions = UserSession::where('user_id', $user->id)
            ->orderByDesc('last_active_at')
            ->get(['id', 'device_name', 'ip_address', 'last_active_at', 'created_at']);

        return ApiResponse::success($sessions);
    }

    /**
     * Revoke a specific session by ID.
     */
    public function revokeSession(Request $request, string $sessionId): JsonResponse
    {
        $user    = $request->user();
        $session = UserSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Revoke the underlying Sanctum token
        $user->tokens()->where('id', $session->token_id)->delete();
        $session->delete();

        return ApiResponse::success(null, __('auth.session_revoked'));
    }

    /**
     * Send password reset link to the given email address.
     * Always returns success to avoid leaking whether an email exists.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);
        Password::sendResetLink(['email' => $request->email]);
        // Always return success (don't reveal if email exists)
        return ApiResponse::success(null, __('passwords.sent'));
    }

    /**
     * Reset the user's password using the provided token.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => ['required', 'string'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => \Hash::make($password)])->save();
                $user->tokens()->delete(); // revoke all sessions for security
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return ApiResponse::success(null, __('passwords.reset'));
        }

        return ApiResponse::error(__('passwords.token'), status: 422);
    }

    /**
     * Build a consistent user array for API responses.
     */
    private function userResource(User $user): array
    {
        return [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'locale'      => $user->locale ?? 'id',
            'tenant_id'   => $user->tenant_id,
            'roles'       => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
        ];
    }
}
