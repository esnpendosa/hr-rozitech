<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Services\AiAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller AiAssistantWebController
 *
 * Mengelola halaman web dan interaksi Asisten AI RMIH (RMIH Lanjutan)
 */
class AiAssistantWebController extends Controller
{
    /**
     * Tampilan utama chat Asisten AI RMIH
     */
    public function index(Request $request, AiAssistantService $aiService): View
    {
        $user = Auth::user();
        $tenant = $user && $user->tenant_id ? Tenant::find($user->tenant_id) : null;
        $context = $aiService->buildTenantContext($tenant, $user);

        $messages = session()->get('ai_chat_history', []);

        // Quick prompt suggestions
        $quickPrompts = [
            'Analisis kehadiran & presensi karyawan hari ini',
            'Bagaimana status koneksi Mesin Absensi X Solutions?',
            'Berapa formula perhitungan upah lembur Depnaker?',
            'Rekomendasi indikator penilaian KPI divisi operasional',
            'Ringkasan inventaris dan aset kantor yang terdaftar',
        ];

        return view('ai.index', compact('tenant', 'user', 'context', 'messages', 'quickPrompts'));
    }

    /**
     * Kirim prompt pertanyaan ke Asisten AI
     */
    public function chat(Request $request, AiAssistantService $aiService): JsonResponse|RedirectResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:3000',
        ]);

        $prompt = trim($request->input('prompt'));
        $user = Auth::user();
        $tenant = $user && $user->tenant_id ? Tenant::find($user->tenant_id) : null;

        $history = session()->get('ai_chat_history', []);

        $result = $aiService->ask($prompt, $tenant, $user, $history);

        $currentTime = now()->timezone(config('app.timezone', 'Asia/Jakarta'))->format('H:i');

        // Simpan percakapan di sesi
        $history[] = [
            'role'       => 'user',
            'content'    => $prompt,
            'created_at' => $currentTime,
        ];
        $history[] = [
            'role'       => 'assistant',
            'content'    => $result['reply'],
            'model'      => $result['model'] ?? 'Asisten RMIH',
            'source'     => $result['source'] ?? 'local',
            'created_at' => $currentTime,
        ];

        // Batasi 20 percakapan terakhir agar sesi tetap ringan
        if (count($history) > 20) {
            $history = array_slice($history, -20);
        }

        session()->put('ai_chat_history', $history);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'reply'   => $result['reply'],
                'model'   => $result['model'],
                'source'  => $result['source'],
                'history' => $history,
            ]);
        }

        return redirect()->route('ai.index');
    }

    /**
     * Hapus riwayat percakapan
     */
    public function clearHistory(Request $request): RedirectResponse
    {
        session()->forget('ai_chat_history');
        return redirect()->route('ai.index')->with('success', 'Riwayat percakapan Asisten AI telah dibersihkan.');
    }
}
