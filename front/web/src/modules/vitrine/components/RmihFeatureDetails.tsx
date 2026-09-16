'use client';

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
    cardBorder: 'border-blue-200/60 dark:border-blue-800/40',
    cardBg: 'bg-blue-50/20 dark:bg-blue-950/10',
    items: [
      'Interaksi otomatis pelanggan & provider',
      'Pembayaran otomatis (tanpa rekap WA)',
      'Tiket via aplikasi/WA/Telegram',
      'Notifikasi gangguan & monitoring',
    ],
  },
  {
    badge: 'Manajemen Pelanggan',
    subBadge: 'Operasional & Billing',
    icon: UserCheck,
    headerColor: 'bg-emerald-600 text-white',
    cardBorder: 'border-emerald-200/60 dark:border-emerald-800/40',
    cardBg: 'bg-emerald-50/20 dark:bg-emerald-950/10',
    items: [
      'Data pelanggan & riwayat',
      'Pembayaran & tagihan',
      'Keluhan & customer service',
      'Registrasi & promo',
    ],
  },
  {
    badge: 'Dokumentasi & Pemetaan',
    subBadge: 'Jaringan & Titik Lokasi',
    icon: Map,
    headerColor: 'bg-amber-600 text-white',
    cardBorder: 'border-amber-200/60 dark:border-amber-800/40',
    cardBg: 'bg-amber-50/20 dark:bg-amber-950/10',
    items: [
      'Ganti password user',
      'Monitoring sinyal',
      'Peta jaringan (map layout)',
      'Koordinat (Tiket)',
    ],
  },
  {
    badge: 'Karyawan & HR',
    subBadge: 'Manajemen Tim',
    icon: Users,
    headerColor: 'bg-purple-600 text-white',
    cardBorder: 'border-purple-200/60 dark:border-purple-800/40',
    cardBg: 'bg-purple-50/20 dark:bg-purple-950/10',
    items: [
      'Fingerprint attendance',
      'Penggajian karyawan',
      'Lembur & insentif',
      'Laporan kerja',
    ],
  },
  {
    badge: 'Inventori & Logistik',
    subBadge: 'Pergudangan & Aset',
    icon: Package,
    headerColor: 'bg-rose-600 text-white',
    cardBorder: 'border-rose-200/60 dark:border-rose-800/40',
    cardBg: 'bg-rose-50/20 dark:bg-rose-950/10',
    items: [
      'Manajemen stok barang',
      'Tool mapping',
      'Penjadwalan status (WA)',
      'Audit aset operasional',
    ],
  },
  {
    badge: 'Lainnya',
    subBadge: 'Integrasi Ekosistem',
    icon: Layers,
    headerColor: 'bg-cyan-600 text-white',
    cardBorder: 'border-cyan-200/60 dark:border-cyan-800/40',
    cardBg: 'bg-cyan-50/20 dark:bg-cyan-950/10',
    items: [
      'Website company profile 1 tahun',
      'SEO + GEO + AIO',
      'Integrasi sistem & notifikasi',
      'Akses API pihak ketiga',
    ],
  },
];

export function RmihFeatureDetails() {
  return (
    <section id="detail-fitur" className="py-16 bg-white dark:bg-slate-950">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-xl mx-auto mb-12">
          <h2 className="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">
            Detail Fitur RMIH
          </h2>
          <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
            Dari manajemen pelanggan hingga operasional, semua terintegrasi dalam satu platform.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {DETAIL_CARDS.map((card, idx) => {
            const Icon = card.icon;
            return (
              <div
                key={idx}
                className={`rounded-2xl border ${card.cardBorder} ${card.cardBg} p-6 flex flex-col justify-between shadow-sm`}
              >
                <div>
                  <div className="flex items-center gap-3 mb-5">
                    <div className={`w-10 h-10 rounded-xl ${card.headerColor} flex items-center justify-center shrink-0`}>
                      <Icon className="w-5 h-5" />
                    </div>
                    <div>
                      <h3 className="text-sm font-bold text-slate-900 dark:text-white">
                        {card.badge}
                      </h3>
                      <p className="text-[11px] text-slate-500 dark:text-slate-400">
                        {card.subBadge}
                      </p>
                    </div>
                  </div>

                  <ul className="space-y-2.5">
                    {card.items.map((item, itemIdx) => (
                      <li key={itemIdx} className="flex items-start gap-2 text-xs text-slate-600 dark:text-slate-300">
                        <Check className="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                        <span>{item}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
