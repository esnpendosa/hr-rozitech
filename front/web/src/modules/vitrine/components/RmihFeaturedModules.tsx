'use client';

import { 
  Bot, 
  MapPin, 
  Users2, 
  UserCheck, 
  Boxes, 
  Globe2, 
  ChevronRight 
} from 'lucide-react';
import Link from 'next/link';

const FEATURED_MODULES = [
  {
    icon: Bot,
    title: 'AI WhatsApp / Telegram Chatbot Automation',
    description: 'Interaksi pelanggan otomatis, pembayaran otomatis, tiket, dan notifikasi gangguan berbasis AI.',
    href: '/#detail-fitur',
  },
  {
    icon: MapPin,
    title: 'Dokumentasi & Pemetaan Jaringan',
    description: 'Ganti password & monitoring sinyal, peta jaringan, dan koordinat (Tiket).',
    href: '/#detail-fitur',
  },
  {
    icon: Users2,
    title: 'Manajemen Tenaga Kerja Otomatis',
    description: 'Absensi fingerprint, gaji, lembur, dan laporan kerja.',
    href: '/#detail-fitur',
  },
  {
    icon: UserCheck,
    title: 'Manajemen Pelanggan',
    description: 'Pembayaran, keluhan, layanan pelanggan, registrasi, promo dan informasi pelanggan.',
    href: '/#detail-fitur',
  },
  {
    icon: Boxes,
    title: 'Inventori & Logistik',
    description: 'Kelola persediaan barang, stok magang, dan penjadwalan status via WA otomatis.',
    href: '/#detail-fitur',
  },
  {
    icon: Globe2,
    title: 'Website Perusahaan',
    description: 'Web company profile 1 tahun full service + SEO, GEO & AIO untuk identitas bisnis Anda.',
    href: '/#detail-fitur',
  },
];

export function RmihFeaturedModules() {
  return (
    <section className="py-16 bg-slate-50/50 dark:bg-slate-900/30">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-xl mx-auto mb-12">
          <h2 className="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">
            Fitur Unggulan
          </h2>
          <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
            Semua yang Anda butuhkan untuk mengelola bisnis dalam satu sistem.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
          {FEATURED_MODULES.map((module, idx) => {
            const Icon = module.icon;
            return (
              <Link
                key={idx}
                href={module.href}
                className="group flex flex-col justify-between p-6 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:border-blue-400 dark:hover:border-blue-600 hover:shadow transition-all"
              >
                <div>
                  <div className="flex items-center justify-between mb-4">
                    <div className="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
                      <Icon className="w-5 h-5" />
                    </div>
                    <ChevronRight className="w-4 h-4 text-blue-500 group-hover:translate-x-1 transition-transform" />
                  </div>

                  <h3 className="text-sm font-bold text-slate-900 dark:text-white mb-2 leading-snug">
                    {module.title}
                  </h3>
                  <p className="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    {module.description}
                  </p>
                </div>
              </Link>
            );
          })}
        </div>
      </div>
    </section>
  );
}
