'use client';

import { motion } from 'framer-motion';
import { 
  Bot, 
  UserCheck, 
  Map, 
  Users, 
  Package, 
  Layers, 
  Check 
} from 'lucide-react';

const DETAIL_CARDS = [
  {
    badge: 'AI Assistant',
    subBadge: 'Chatbot WA/Telegram',
    icon: Bot,
    headerColor: 'bg-blue-600 text-white',
    cardBorder: 'border-blue-200 dark:border-blue-800',
    cardBg: 'bg-blue-50/40 dark:bg-blue-950/20',
    items: [
      'Interaksi otomatis pelanggan & tim lapangan',
      'Pengingat absensi otomatis via WhatsApp',
      'Tiket kendala operasional mandiri',
      'Notifikasi pembaruan sistem instan',
    ],
  },
  {
    badge: 'Manajemen Karyawan',
    subBadge: 'Administrasi Personalia',
    icon: UserCheck,
    headerColor: 'bg-emerald-600 text-white',
    cardBorder: 'border-emerald-200 dark:border-emerald-800',
    cardBg: 'bg-emerald-50/40 dark:bg-emerald-950/20',
    items: [
      'Database profil & dokumen karyawan',
      'Struktur organisasi & pembagian shift',
      'Manajemen kontrak & masa percobaan',
      'Pengajuan izin, sakit, & cuti mandiri',
    ],
  },
  {
    badge: 'Presensi & Pemetaan',
    subBadge: 'GPS & Biometrik',
    icon: Map,
    headerColor: 'bg-amber-600 text-white',
    cardBorder: 'border-amber-200 dark:border-amber-800',
    cardBg: 'bg-amber-50/40 dark:bg-amber-950/20',
    items: [
      'Absensi selfie dengan radius geofencing',
      'Sinkronisasi mesin ZKTeco offline-ready',
      'Peta sebaran tim lapangan (live location)',
      'Validasi kehadiran bebas kecurangan (anti fake GPS)',
    ],
  },
  {
    badge: 'Penggajian & HR',
    subBadge: 'Payroll Otomatis',
    icon: Users,
    headerColor: 'bg-purple-600 text-white',
    cardBorder: 'border-purple-200 dark:border-purple-800',
    cardBg: 'bg-purple-50/40 dark:bg-purple-950/20',
    items: [
      'Kalkulasi PPh 21 TER otomatis',
      'Perhitungan BPJS Kesehatan & Ketenagakerjaan',
      'Rekap lembur, potongan & insentif kinerja',
      'Kirim slip gaji digital langsung ke karyawan',
    ],
  },
  {
    badge: 'Inventori & Logistik',
    subBadge: 'Aset & Pengadaan',
    icon: Package,
    headerColor: 'bg-rose-600 text-white',
    cardBorder: 'border-rose-200 dark:border-rose-800',
    cardBg: 'bg-rose-50/40 dark:bg-rose-950/20',
    items: [
      'Pencatatan inventaris alat & perangkat kerja',
      'Tracking peminjaman dan pengembalian barang',
      'Notifikasi stok perlengkapan habis',
      'Laporan audit aset tahunan perusahaan',
    ],
  },
  {
    badge: 'Fitur Khusus Industri',
    subBadge: 'Ekosistem Lengkap',
    icon: Layers,
    headerColor: 'bg-cyan-600 text-white',
    cardBorder: 'border-cyan-200 dark:border-cyan-800',
    cardBg: 'bg-cyan-50/40 dark:bg-cyan-950/20',
    items: [
      'Modul Restoran & POS kasir terintegrasi',
      'Modul Travel & Agen pemesanan tiket',
      'Modul Edukasi & absensi siswa/guru',
      'Integrasi API pihak ketiga & webhooks',
    ],
  },
];

export function RmihFeatureDetails() {
  return (
    <section id="detail-fitur" className="py-20 bg-white dark:bg-slate-950">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-2xl mx-auto mb-16">
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">
            Detail Fitur RMIH
          </h2>
          <p className="text-base text-slate-500 dark:text-slate-400">
            Dari manajemen karyawan hingga operasional lapangan, semua terintegrasi dalam satu platform.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
          {DETAIL_CARDS.map((card, idx) => {
            const Icon = card.icon;
            return (
              <motion.div
                key={idx}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: idx * 0.08 }}
                className={`rounded-2xl border ${card.cardBorder} ${card.cardBg} p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-all`}
              >
                <div>
                  {/* Header Tag */}
                  <div className="flex items-center gap-3 mb-6">
                    <div className={`w-11 h-11 rounded-xl ${card.headerColor} flex items-center justify-center shrink-0 shadow-sm`}>
                      <Icon className="w-5 h-5" />
                    </div>
                    <div>
                      <h3 className="text-base font-bold text-slate-900 dark:text-white">
                        {card.badge}
                      </h3>
                      <p className="text-xs text-slate-500 dark:text-slate-400">
                        {card.subBadge}
                      </p>
                    </div>
                  </div>

                  {/* Checklist Points */}
                  <ul className="space-y-3 mb-4">
                    {card.items.map((item, itemIdx) => (
                      <li key={itemIdx} className="flex items-start gap-2.5 text-xs sm:text-sm text-slate-700 dark:text-slate-300">
                        <Check className="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                        <span>{item}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
