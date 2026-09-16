'use client';

import { motion } from 'framer-motion';
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
    title: 'AI WhatsApp / Notifikasi Otomatis',
    description: 'Notifikasi absensi real-time, broadcast pengumuman perusahaan, dan pengingat via WhatsApp.',
    tagColor: 'bg-blue-500 text-white',
    href: '/#detail-fitur',
  },
  {
    icon: MapPin,
    title: 'GPS Tracking & Presensi Multi-Cabang',
    description: 'Geofencing akurat, verifikasi foto selfie, dan monitoring persebaran staf lapangan.',
    tagColor: 'bg-emerald-500 text-white',
    href: '/#detail-fitur',
  },
  {
    icon: Users2,
    title: 'Manajemen Tenaga Kerja Otomatis',
    description: 'Absensi sidik jari & wajah ZKTeco, slip gaji otomatis, lembur, dan rekapitulasi jam kerja.',
    tagColor: 'bg-indigo-500 text-white',
    href: '/#detail-fitur',
  },
  {
    icon: UserCheck,
    title: 'Portal Mandiri Karyawan (Self-Service)',
    description: 'Pengajuan cuti, izin sakit, klaim reimburse, dan mutasi tugas mandiri via aplikasi mobile.',
    tagColor: 'bg-purple-500 text-white',
    href: '/#detail-fitur',
  },
  {
    icon: Boxes,
    title: 'Inventori, Aset & Logistik',
    description: 'Kelola inventaris aset kantor, peminjaman alat kerja, dan pelacakan status operasional.',
    tagColor: 'bg-rose-500 text-white',
    href: '/#detail-fitur',
  },
  {
    icon: Globe2,
    title: 'Multi-Tenant & Multi-Industri',
    description: 'Mendukung vertikal Restoran, Travel, Edukasi, SPBU, serta integrasi API kustom.',
    tagColor: 'bg-cyan-500 text-white',
    href: '/#detail-fitur',
  },
];

export function RmihFeaturedModules() {
  return (
    <section className="py-20 bg-slate-50/60 dark:bg-slate-900/30 border-t border-slate-200/70 dark:border-slate-800/70">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-2xl mx-auto mb-16">
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">
            Fitur Unggulan
          </h2>
          <p className="text-base text-slate-500 dark:text-slate-400">
            Semua yang Anda butuhkan untuk mengelola operasional dalam satu sistem terpusat.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {FEATURED_MODULES.map((module, idx) => {
            const Icon = module.icon;
            return (
              <motion.div
                key={idx}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: idx * 0.08 }}
              >
                <Link
                  href={module.href}
                  className="group flex flex-col justify-between h-full p-6 sm:p-7 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-md hover:border-blue-300 dark:hover:border-blue-700 transition-all"
                >
                  <div>
                    <div className="flex items-center justify-between mb-5">
                      <div className="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                        <Icon className="w-6 h-6" />
                      </div>
                      <ChevronRight className="w-5 h-5 text-slate-400 group-hover:text-blue-600 group-hover:translate-x-1 transition-all" />
                    </div>

                    <h3 className="text-base sm:text-lg font-bold text-slate-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                      {module.title}
                    </h3>
                    <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                      {module.description}
                    </p>
                  </div>
                </Link>
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
