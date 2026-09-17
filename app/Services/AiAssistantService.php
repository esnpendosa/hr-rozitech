<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\FingerprintDevice;
use App\Models\InventoryItem;
use App\Models\PayrollRecord;
use App\Models\Tenant;
use App\Models\User;
use App\Models\WorkShift;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service AiAssistantService
 *
 * Mengelola interaksi Asisten AI RMIH (RMIH Lanjutan) menggunakan
 * OpenRouter Free Tier (misal meta-llama/llama-3.2-3b-instruct:free)
 * dengan Fallback Cerdas berbasis Konteks Nyata Organisasi & Mesin Absensi X Solutions.
 */
class AiAssistantService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key') ?? (string) env('OPENROUTER_API_KEY', '');
        $this->model = config('services.openrouter.model') ?? 'meta-llama/llama-3.2-3b-instruct:free';
        $this->baseUrl = config('services.openrouter.base_url') ?? 'https://openrouter.ai/api/v1';
    }

    /**
     * Kirim pesan ke Asisten AI RMIH
     */
    public function ask(string $prompt, ?Tenant $tenant = null, ?User $user = null, array $conversationHistory = []): array
    {
        $context = $this->buildTenantContext($tenant, $user);

        // Jika API Key OpenRouter dikonfigurasi, coba panggil OpenRouter
        if (!empty($this->apiKey)) {
            try {
                $response = $this->callOpenRouter($prompt, $context, $conversationHistory);
                if ($response['success']) {
                    return [
                        'success' => true,
                        'reply'   => $response['reply'],
                        'source'  => 'openrouter',
                        'model'   => $this->model,
                    ];
                }
            } catch (\Throwable $e) {
                Log::warning('OpenRouter API request failed, switching to RMIH Enterprise Knowledge Engine: ' . $e->getMessage());
            }
        }

        // Fallback Cerdas: RMIH Enterprise Context & Knowledge Engine
        $fallbackReply = $this->generateLocalIntelligenceReply($prompt, $context, $tenant);

        return [
            'success' => true,
            'reply'   => $fallbackReply,
            'source'  => 'rmih_engine',
            'model'   => 'Asisten RMIH',
        ];
    }

    /**
     * Panggil API OpenRouter dengan model Free Tier
     */
    protected function callOpenRouter(string $prompt, array $context, array $conversationHistory = []): array
    {
        $systemPrompt = <<<EOT
Anda adalah asisten manajemen SDM dan operasional di platform RMIH untuk organisasi {$context['tenant_name']}.
Tugas Anda adalah memberikan jawaban yang natural, praktis, ramah, dan akurat sesuai data nyata organisasi dan ketentuan ketenagakerjaan Indonesia.
ATURAN KOMUNIKASI:
1. Berikan jawaban yang natural, sopan, dan solutif layaknya konsultan HR/operasional profesional.
2. JANGAN menyebut diri Anda model AI, jangan menyebut versi sistem, engine, atau istilah teknis yang tidak perlu.
3. JANGAN menggunakan emoji atau stiker. Gunakan bahasa Indonesia yang baik, rapi, dan mudah dipahami.
4. Gunakan data live organisasi berikut secara tepat dan jujur:
- Nama Perusahaan: {$context['tenant_name']}
- Total Karyawan: {$context['total_employees']} orang
- Hadir Hari Ini: {$context['present_today']} orang
- Izin / Cuti Hari Ini: {$context['leaves_today']} orang
- Mesin Absensi X Solutions: {$context['total_devices']} unit terdaftar ({$context['online_devices']} online)
- Jika data karyawan masih 0, sampaikan apa adanya secara wajar dan jelaskan langkah untuk mendaftarkan karyawan.
EOT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];

        // Masukkan history percakapan terakhir jika ada
        foreach (array_slice($conversationHistory, -4) as $msg) {
            $messages[] = [
                'role'    => $msg['role'] ?? 'user',
                'content' => $msg['content'] ?? '',
            ];
        }

        $messages[] = ['role' => 'user', 'content' => $prompt];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type'  => 'application/json',
            'HTTP-Referer'   => config('app.url', 'https://rmih.app'),
            'X-Title'       => 'RMIH Enterprise Platform',
        ])->timeout(20)->post($this->baseUrl . '/chat/completions', [
            'model'       => $this->model,
            'messages'    => $messages,
            'temperature' => 0.3,
            'max_tokens'  => 1200,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $reply = $data['choices'][0]['message']['content'] ?? null;
            if ($reply) {
                return ['success' => true, 'reply' => trim($reply)];
            }
        }

        return ['success' => false, 'error' => $response->body()];
    }

    /**
     * Kumpulkan konteks data live organisasi
     */
    public function buildTenantContext(?Tenant $tenant, ?User $user): array
    {
        if (!$tenant) {
            return [
                'tenant_name'         => 'RMIH Demo Workspace',
                'total_employees'     => 0,
                'present_today'       => 0,
                'leaves_today'        => 0,
                'total_devices'       => 0,
                'online_devices'      => 0,
                'subscription_status' => 'active',
                'departments'         => [],
            ];
        }

        $tenantId = $tenant->id;
        $today = Carbon::today();

        $totalEmployees = Employee::where('tenant_id', $tenantId)->count();
        $departments = Department::where('tenant_id', $tenantId)->pluck('name')->toArray();

        // Kehadiran hari ini
        $todayAttendance = AttendanceRecord::where('tenant_id', $tenantId)
            ->whereDate('attendance_date', $today)
            ->get();

        $presentCount = $todayAttendance->whereIn('status', ['present', 'late'])->count();
        $leavesCount = $todayAttendance->whereIn('status', ['leave', 'sick', 'permit'])->count();

        // Mesin Absensi X Solutions
        $devices = FingerprintDevice::where('tenant_id', $tenantId)->get();
        $totalDevices = $devices->count();
        $onlineDevices = $devices->where('status', 'online')->count();

        // Shift Aktif
        $shiftsCount = WorkShift::where('tenant_id', $tenantId)->count();

        // Inventaris Aset
        $totalAssets = InventoryItem::where('tenant_id', $tenantId)->count();

        return [
            'tenant_name'         => $tenant->name,
            'user_name'           => $user?->name ?? 'Administrator',
            'total_employees'     => $totalEmployees,
            'present_today'       => $presentCount,
            'leaves_today'        => $leavesCount,
            'total_devices'       => $totalDevices,
            'online_devices'      => $onlineDevices,
            'shifts_count'        => $shiftsCount,
            'total_assets'        => $totalAssets,
            'departments'         => $departments,
            'subscription_status' => $tenant->subscription?->status ?? 'active',
        ];
    }

    /**
     * Fallback Cerdas: Jawaban terstruktur berbasis analitik RMIH & regulasi Indonesia
     */
    protected function generateLocalIntelligenceReply(string $prompt, array $ctx, ?Tenant $tenant): string
    {
        $p = strtolower($prompt);

        // 1. Kueri Mesin Absensi X Solutions & ADMS (prioritas sebelum absensi umum)
        if (str_contains($p, 'mesin') || str_contains($p, 'x solution') || str_contains($p, 'fingerprint') || str_contains($p, 'adms') || str_contains($p, 'alat')) {
            if ($ctx['total_devices'] === 0) {
                return "Saat ini **belum ada Mesin Absensi X Solutions** yang terdaftar di **{$ctx['tenant_name']}**.\n\n" .
                    "Panduan menghubungkan Mesin Absensi X Solutions via ADMS Cloud Push:\n" .
                    "1. Buka menu **Mesin Absensi** lalu klik tombol **Tambah Perangkat**.\n" .
                    "2. Isi nomor seri (SN) mesin dan tentukan lokasi pemasangan.\n" .
                    "3. Pada unit mesin fisik X Solutions, buka menu **Comm. Settings > Cloud Server Setting**:\n" .
                    "   - **Server Address**: `" . request()->getHost() . "`\n" .
                    "   - **Server Port**: `80`\n" .
                    "   - Aktifkan opsi **Enable Cloud Push / ADMS**.\n" .
                    "4. Setelah terhubung, setiap transaksi scan sidik jari atau wajah akan otomatis tersinkronisasi ke sistem.";
            }

            return "Status integrasi Mesin Absensi X Solutions di **{$ctx['tenant_name']}**:\n\n" .
                "- **Total Mesin Terdaftar**: {$ctx['total_devices']} unit\n" .
                "- **Mesin Terhubung (Online)**: {$ctx['online_devices']} unit\n" .
                "- **Protokol**: ADMS Cloud Push (Real-Time)\n\n" .
                "Jika ada perangkat yang offline, pastikan kabel LAN atau Wi-Fi mesin aktif dan menu Cloud Server di mesin mengarah ke `" . request()->getHost() . "`.";
        }

        // 2. Kueri Kehadiran & Presensi
        if (str_contains($p, 'hadir') || str_contains($p, 'absen') || str_contains($p, 'kehadiran') || str_contains($p, 'presensi')) {
            if ($ctx['total_employees'] === 0) {
                return "Saat ini **belum ada data karyawan** yang terdaftar di **{$ctx['tenant_name']}**, sehingga belum ada catatan presensi untuk hari ini.\n\n" .
                    "Langkah untuk mulai mencatat kehadiran:\n" .
                    "1. Masukkan data karyawan melalui menu **Karyawan > Tambah Karyawan**.\n" .
                    "2. Atur jam kerja pada menu **Absensi > Pengaturan Shift**.\n" .
                    "3. Hubungkan perangkat pada menu **Mesin Absensi X Solutions** atau gunakan absensi mobile dengan validasi GPS.";
            }

            $persentase = round(($ctx['present_today'] / $ctx['total_employees']) * 100, 1);
            $belumHadir = max(0, $ctx['total_employees'] - $ctx['present_today'] - $ctx['leaves_today']);

            $reply = "Ringkasan kehadiran karyawan di **{$ctx['tenant_name']}** hari ini:\n\n" .
                "- **Total Karyawan**: {$ctx['total_employees']} orang\n" .
                "- **Hadir**: {$ctx['present_today']} orang ({$persentase}%)\n" .
                "- **Izin / Sakit / Cuti**: {$ctx['leaves_today']} orang\n" .
                "- **Belum Tercatat**: {$belumHadir} orang\n\n";

            if ($ctx['total_devices'] > 0) {
                $reply .= "Status perangkat Mesin Absensi X Solutions: **{$ctx['online_devices']} dari {$ctx['total_devices']} unit** dalam kondisi online.";
            } else {
                $reply .= "Belum ada Mesin Absensi X Solutions yang terhubung. Karyawan saat ini dapat melakukan presensi melalui aplikasi mobile.";
            }

            return $reply;
        }

        // 3. Kueri Lembur
        if (str_contains($p, 'lembur') || str_contains($p, 'overtime')) {
            return "Ketentuan perhitungan upah lembur mengacu pada **PP No. 35 Tahun 2021**:\n\n" .
                "**1. Dasar Perhitungan Upah Sejam:**\n" .
                "`1 / 173 × Upah Sebulan` (Gaji Pokok + Tunjangan Tetap).\n\n" .
                "**2. Lembur pada Hari Kerja Biasa:**\n" .
                "- **Jam pertama**: `1,5 × Upah Sejam`\n" .
                "- **Jam kedua dan seterusnya**: `2,0 × Upah Sejam`\n\n" .
                "**3. Lembur pada Hari Istirahat Mingguan / Libur Resmi (5 hari kerja):**\n" .
                "- **Jam 1 s.d. 8**: `2 × Upah Sejam`\n" .
                "- **Jam ke-9**: `3 × Upah Sejam`\n" .
                "- **Jam 10 s.d. 12**: `4 × Upah Sejam`\n\n" .
                "Di RMIH, durasi lembur dihitung otomatis berdasarkan data kepulangan absensi setelah pengajuan Surat Perintah Lembur (SPL) disetujui.";
        }

        // 4. Kueri PPh 21 TER & Pajak
        if (str_contains($p, 'pph') || str_contains($p, 'ter') || str_contains($p, 'pajak')) {
            return "Perhitungan PPh Pasal 21 di RMIH menggunakan skema **Tarif Efektif Rata-Rata (TER)** sesuai **PP No. 58/2023** dan **PMK No. 168/2023**:\n\n" .
                "**1. Pengelompokan Kategori TER:**\n" .
                "- **TER A**: TK/0 (PTKP Rp54 jt), TK/1 & K/0 (PTKP Rp58,5 jt)\n" .
                "- **TER B**: TK/2 & K/1 (PTKP Rp63 jt), TK/3 & K/2 (PTKP Rp67,5 jt)\n" .
                "- **TER C**: K/3 (PTKP Rp72 jt)\n\n" .
                "**2. Masa Pajak Bulanan (Januari – November):**\n" .
                "`PPh 21 Bulanan = Penghasilan Bruto Sebulan × Tarif Efektif (TER)`\n" .
                "*(Tarif mulai dari 0% untuk penghasilan hingga Rp5,4 juta/bulan, berjenjang sesuai tabel).* \n\n" .
                "**3. Masa Pajak Terakhir (Desember):**\n" .
                "Dihitung ulang dengan tarif progresif Pasal 17 UU PPh atas seluruh penghasilan neto setahun dikurangi PTKP, kemudian dikurangi total PPh 21 yang telah dipotong pada bulan Januari s.d. November.\n\n" .
                "Sistem Payroll RMIH secara otomatis mencocokkan kategori TER sesuai data status pernikahan dan tanggungan yang ada di data karyawan.";
        }

        // 5. Kueri Penggajian / Payroll Umum
        if (str_contains($p, 'gaji') || str_contains($p, 'payroll')) {
            return "Alur proses penggajian di platform RMIH:\n\n" .
                "1. **Rekap Kehadiran**: Menghitung potongan keterlambatan atau ketidakhadiran dari log absensi harian.\n" .
                "2. **Kalkulasi Lembur**: Mengonversi jam lembur yang telah disetujui sesuai aturan PP 35/2021.\n" .
                "3. **Pemotongan PPh 21 TER**: Menggunakan tarif efektif bulanan sesuai status PTKP masing-masing karyawan.\n" .
                "4. **Slip Gaji Digital**: Membuat slip gaji otomatis yang dapat diakses karyawan via aplikasi.\n\n" .
                "Penggajian dapat diproses langsung melalui menu **Payroll & Keuangan**.";
        }

        // 6. Kueri Inventaris & Aset
        if (str_contains($p, 'aset') || str_contains($p, 'inventaris') || str_contains($p, 'barang') || str_contains($p, 'stok')) {
            if ($ctx['total_assets'] === 0) {
                return "Saat ini **belum ada aset atau inventaris** yang dicatat untuk **{$ctx['tenant_name']}**.\n\n" .
                    "Anda dapat mencatat peralatan kerja, laptop, dan perlengkapan operasional pada menu **Inventaris & Aset** untuk memudahkan pelacakan penanggung jawab dan riwayat peminjaman.";
            }

            return "Data inventaris perusahaan saat ini:\n\n" .
                "- **Total Aset Tercatat**: {$ctx['total_assets']} item\n" .
                "- **Pengelolaan**: Setiap aset dapat dihubungkan ke karyawan penanggung jawab dengan pencatatan nomor seri dan riwayat serah terima.\n\n" .
                "Selengkapnya dapat dikelola melalui menu **Inventaris & Aset**.";
        }

        // 7. Kueri KPI & Evaluasi Kinerja
        if (str_contains($p, 'kpi') || str_contains($p, 'performa') || str_contains($p, 'evaluasi') || str_contains($p, 'kinerja')) {
            $deptList = !empty($ctx['departments']) ? implode(', ', $ctx['departments']) : 'Umum, Operasional';
            return "Struktur penilaian KPI karyawan di RMIH dirancang terukur dan transparan:\n\n" .
                "**Komponen & Bobot Rekomendasi:**\n" .
                "- **Pencapaian Target Kerja (50%)**: Dinilai dari penyelesaian tugas dan progres target bulanan.\n" .
                "- **Kedisiplinan & Presensi (25%)**: Dinilai dari rasio ketepatan waktu hadir melalui log mesin absensi.\n" .
                "- **Kerja Sama & Kompetensi (25%)**: Penilaian atasan langsung terhadap kualitas kerja dan inisiatif.\n\n" .
                "Template indikator dapat disesuaikan untuk masing-masing divisi ({$deptList}) pada menu **KPI & Performa**.";
        }

        // 8. Respon Default yang Natural
        return "Halo! Saya asisten operasional dan data untuk **{$ctx['tenant_name']}**.\n\n" .
            "Beberapa hal yang dapat saya bantu:\n" .
            "- Cek status kehadiran dan presensi karyawan hari ini\n" .
            "- Panduan pengaturan Mesin Absensi X Solutions (ADMS Cloud)\n" .
            "- Ketentuan upah lembur PP 35/2021 dan simulasi PPh 21 TER\n" .
            "- Pengelolaan KPI, target kerja, dan inventaris aset\n\n" .
            "Silakan ketik pertanyaan Anda atau pilih salah satu topik di panel sebelah kiri.";
    }
}
