<?php

namespace Database\Seeders;

use App\Models\PlanFeature;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

/**
 * Class SubscriptionPlanSeeder
 *
 * Mengonfigurasi paket langganan persis sesuai spesifikasi Gambar 2:
 * 1. Free: Rp 0, hingga 5 karyawan, 100% gratis selamanya
 * 2. Pilot: Rp 410.000/bln (tahunan) / Rp 490.000 (bulanan) hemat 17%, hingga 30 karyawan
 * 3. Operations: Rp 1.119.000/bln (tahunan) / Rp 1.349.000 (bulanan) hemat 17%, hingga 200 karyawan, PALING POPULER
 * 4. Enterprise: Custom / Hubungi Kami, karyawan tanpa batas
 */
class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus paket gratis lama jika ada
        SubscriptionPlan::where('slug', 'free')->delete();

        $plans = [
            [
                'name'          => 'Starter',
                'slug'          => 'starter',
                'description'   => 'Solusi esensial untuk bisnis pemula dan UKM yang ingin merapikan operasional tim',
                'price'         => 299000,
                'annual_price'  => 249000,
                'currency'      => 'IDR',
                'billing_cycle' => 'monthly',
                'badge'         => null,
                'is_active'     => true,
                'features'      => [
                    'max_employees'                                 => 'Hingga 25 Karyawan',
                    'Absensi Web & Mobile GPS Presisi'              => 'true',
                    'Manajemen Cuti, Izin & Saldo Otomatis'         => 'true',
                    'Berkas Karyawan & Dokumen HR Digital'          => 'true',
                    'Integrasi Mesin Absensi X Solutions Dasar'     => 'true',
                    'Slip Gaji PDF & Laporan Kehadiran Standar'     => 'true',
                    'Aplikasi Mobile Android & iOS'                 => 'true',
                    'Bantuan Teknis via Email (respon cepat)'       => 'true',
                ],
            ],
            [
                'name'          => 'Operations',
                'slug'          => 'operations',
                'description'   => 'Solusi lengkap untuk bisnis berkembang yang mengelola tim lapangan, kantor & multi-shift',
                'price'         => 899000,
                'annual_price'  => 749000,
                'currency'      => 'IDR',
                'billing_cycle' => 'monthly',
                'badge'         => 'PALING POPULER',
                'is_active'     => true,
                'features'      => [
                    'max_employees'                                              => 'Hingga 150 Karyawan',
                    'Semua fitur di paket Starter, ditambah:'                    => 'true',
                    'Penggajian Otomatis (Payroll) & Validasi HR'                => 'true',
                    'Alur Persetujuan Bertingkat & Multi-Manajer'                => 'true',
                    'Integrasi Mesin Absensi X Solutions (ADMS Cloud Push)'       => 'true',
                    'Modul Akuntansi & Laporan Keuangan RMIH'                    => 'true',
                    'Modul Inventaris & Manajemen Aset Operasional'              => 'true',
                    'Analitik Kinerja SDM & Ekspor Laporan Lanjutan'             => 'true',
                    'Akses REST API & Webhooks Terbuka'                          => 'true',
                    'Dukungan Prioritas via Chat/Email 24/7'                     => 'true',
                ],
            ],
            [
                'name'          => 'Enterprise',
                'slug'          => 'enterprise',
                'description'   => 'Untuk perusahaan multi-cabang, waralaba, pabrik dan operasional skala besar',
                'price'         => 2499000,
                'annual_price'  => 1999000,
                'currency'      => 'IDR',
                'billing_cycle' => 'monthly',
                'badge'         => 'ENTERPRISE',
                'is_active'     => true,
                'features'      => [
                    'max_employees'                                              => 'Karyawan Tanpa Batas',
                    'Semua fitur di paket Operations, ditambah:'                 => 'true',
                    'Multi-Cabang & Multi-Entitas Perusahaan'                    => 'true',
                    'Single Sign-On (SSO SAML / OIDC)'                           => 'true',
                    'Integrasi Multi-Mesin Absensi X Solutions Terpusat'         => 'true',
                    'SLA Terjamin 99.9%, Pendampingan Migrasi & Pelatihan'       => 'true',
                    'Lingkungan Server Khusus / Private Cloud Pilihan'           => 'true',
                    'Audit Trail Lengkap & Ekspor Kepatuhan ISO'                 => 'true',
                    'Konektor ERP/Akuntansi Kustom & Fitur RMIH Lanjutan'        => 'true',
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $features = $planData['features'];
            unset($planData['features']);

            $plan = SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );

            // Perbarui fitur paket
            PlanFeature::where('plan_id', $plan->id)->delete();
            foreach ($features as $key => $val) {
                PlanFeature::create([
                    'plan_id'       => $plan->id,
                    'feature_key'   => $key,
                    'feature_value' => $val,
                ]);
            }
        }
    }
}
