<?php

namespace App\Http\Controllers\Api\V1\Fingerprint;

use App\Application\Services\ApiResponse;
use App\Domain\Fingerprint\Services\FingerprintSyncService;
use App\Http\Controllers\Controller;
use App\Integrations\Fingerprint\Contracts\FingerprintProvider;
use App\Jobs\SyncFingerprintJob;
use App\Models\Employee;
use App\Models\FingerprintDevice;
use App\Models\FingerprintMapping;
use App\Models\FingerprintSyncRun;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class FingerprintDeviceController
 *
 * Mengelola interaksi dengan perangkat fisik absensi sidik jari:
 * - Registrasi mesin (IP, port, serial number, cabang)
 * - Health check / uji koneksi
 * - Pemetaan ID enroll mesin ke ID pegawai
 * - Trigger sinkronisasi data presensi
 */
class FingerprintDeviceController extends Controller
{
    public function __construct(
        protected FingerprintProvider $provider,
        protected FingerprintSyncService $syncService
    ) {}

    /**
     * Menampilkan daftar perangkat fingerprint yang terdaftar pada tenant aktif.
     */
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $devices = FingerprintDevice::where('tenant_id', $tenantId)
            ->with(['branch'])
            ->withCount(['deviceUsers', 'syncRuns'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ApiResponse::paginated($devices);
    }

    /**
     * Mendaftarkan unit mesin fingerprint baru.
     */
    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $validated = $request->validate([
            'branch_id'     => 'required|uuid|exists:branches,id',
            'device_name'   => 'nullable|string|max:100',
            'name'          => 'nullable|string|max:100',
            'device_sn'     => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'ip_address'    => 'nullable|ip',
            'port'          => 'nullable|integer|min:1|max:65535',
            'protocol'      => 'nullable|string|in:tcp,udp,cloud',
            'api_endpoint'  => 'nullable|url',
            'api_url'       => 'nullable|url',
            'api_key'       => 'nullable|string',
            'location_name' => 'nullable|string|max:100',
            'provider'      => 'nullable|string',
            'is_active'     => 'boolean',
        ]);

        $name = $validated['name'] ?? $validated['device_name'] ?? 'Mesin Absensi';
        $serialNumber = $validated['serial_number'] ?? $validated['device_sn'] ?? 'SN-' . time();

        $device = FingerprintDevice::create([
            'tenant_id'     => $tenantId,
            'branch_id'     => $validated['branch_id'],
            'name'          => $name,
            'serial_number' => $serialNumber,
            'ip_address'    => $validated['ip_address'] ?? null,
            'port'          => $validated['port'] ?? 4370,
            'api_url'       => $validated['api_url'] ?? $validated['api_endpoint'] ?? null,
            'api_key'       => $validated['api_key'] ?? null,
            'provider'      => $validated['provider'] ?? 'xsolutions',
            'status'        => 'active',
            'extra_config'  => [
                'location_name' => $validated['location_name'] ?? null,
                'protocol'      => $validated['protocol'] ?? 'tcp',
            ],
        ]);

        return ApiResponse::created($device, 'Perangkat fingerprint berhasil didaftarkan');
    }

    /**
     * Menampilkan detail perangkat beserta statistik sinkronisasi terakhir.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $device = FingerprintDevice::where('tenant_id', $tenantId)
            ->with(['branch', 'syncRuns' => fn($q) => $q->latest()->limit(5)])
            ->findOrFail($id);

        return ApiResponse::success($device, 'Detail perangkat berhasil diambil');
    }

    /**
     * Memperbarui informasi konfigurasi perangkat.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $device = FingerprintDevice::where('tenant_id', $tenantId)->findOrFail($id);

        $validated = $request->validate([
            'branch_id'     => 'sometimes|uuid|exists:branches,id',
            'name'          => 'sometimes|string|max:100',
            'serial_number' => 'sometimes|string|max:100',
            'ip_address'    => 'nullable|ip',
            'port'          => 'nullable|integer|min:1|max:65535',
            'api_url'       => 'nullable|url',
            'api_key'       => 'nullable|string',
            'status'        => 'nullable|string',
        ]);

        $device->update($validated);

        return ApiResponse::success($device, 'Perangkat berhasil diperbarui');
    }

    /**
     * Menghapus registrasi perangkat.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $device = FingerprintDevice::where('tenant_id', $tenantId)->findOrFail($id);
        $device->delete();

        return ApiResponse::success(null, 'Perangkat berhasil dihapus');
    }

    /**
     * Uji ping dan konektivitas soket/API ke mesin fingerprint fisik.
     */
    public function testConnection(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $device = FingerprintDevice::where('tenant_id', $tenantId)->findOrFail($id);
        $connected = $this->provider->testConnection($device);

        $device->update([
            'status'       => $connected ? 'active' : 'error',
            'last_seen_at' => now(),
        ]);

        if ($connected) {
            return ApiResponse::success(['status' => 'active', 'connected' => true], 'Koneksi ke perangkat berhasil');
        }

        return ApiResponse::error('Koneksi ke perangkat gagal atau timeout', 503, [
            'status' => 'error',
            'connected' => false,
        ]);
    }

    /**
     * Memicu sinkronisasi log penarikan presensi (bisa synchronously atau background queue).
     */
    public function triggerSync(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $device = FingerprintDevice::where('tenant_id', $tenantId)->findOrFail($id);

        if ($request->boolean('async', true)) {
            SyncFingerprintJob::dispatch($device->id, $tenantId);
            return ApiResponse::success([
                'device_id' => $device->id,
                'status'    => 'queued',
            ], 'Antrian sinkronisasi sidik jari telah dijadwalkan');
        }

        $syncRun = $this->syncService->syncDevice($device);

        return ApiResponse::success($syncRun, 'Sinkronisasi sidik jari selesai');
    }

    /**
     * Mengambil daftar log audit sinkronisasi suatu mesin.
     */
    public function syncLogs(Request $request, string $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $device = FingerprintDevice::where('tenant_id', $tenantId)->findOrFail($id);

        $logs = FingerprintSyncRun::where('device_id', $device->id)
            ->with(['errors'])
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return ApiResponse::paginated($logs);
    }

    /**
     * Memetakan User ID / Enroll PIN pada mesin sidik jari ke Employee ID.
     */
    public function mapEmployee(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $validated = $request->validate([
            'employee_id'           => 'required|uuid|exists:employees,id',
            'fingerprint_device_id' => 'required|uuid|exists:fingerprint_devices,id',
            'device_pin'            => 'nullable|string|max:50',
            'external_id'           => 'nullable|string|max:50',
        ]);

        $devicePin = $validated['external_id'] ?? $validated['device_pin'];

        // Verifikasi kepemilikan tenant
        $device = FingerprintDevice::where('tenant_id', $tenantId)->findOrFail($validated['fingerprint_device_id']);
        $employee = Employee::where('tenant_id', $tenantId)->findOrFail($validated['employee_id']);

        $mapping = FingerprintMapping::updateOrCreate(
            [
                'tenant_id'   => $tenantId,
                'device_id'   => $device->id,
                'external_id' => $devicePin,
            ],
            [
                'employee_id' => $employee->id,
                'is_active'   => true,
            ]
        );

        return ApiResponse::success($mapping, 'Pemetaan PIN pegawai pada mesin sidik jari berhasil disimpan');
    }

    /**
     * Mengambil seluruh pemetaan biometrik dalam tenant.
     */
    public function getMappings(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $mappings = FingerprintMapping::where('tenant_id', $tenantId)
            ->with(['employee.user', 'device'])
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return ApiResponse::paginated($mappings);
    }
}
