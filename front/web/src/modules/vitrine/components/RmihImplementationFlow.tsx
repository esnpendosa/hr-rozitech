'use client';

import { motion } from 'framer-motion';
import { UserCheck, Search, Wrench, CheckCircle2, ShieldCheck, ChevronRight } from 'lucide-react';

const STEPS = [
  {
    step: '1',
    icon: UserCheck,
    title: 'Registrasi Klien',
    desc: 'Pengisian data awal dan konsultasi kebutuhan bisnis Anda.',
  },
  {
    step: '2',
    icon: Search,
    title: 'Analisis & Setup',
    desc: 'Penyesuaian struktur shift, cabang, dan sistem gaji.',
  },
  {
    step: '3',
    icon: Wrench,
    title: 'Instalasi',
    desc: 'Pengaturan perangkat, mesin absensi, dan mobile app.',
  },
  {
    step: '4',
    icon: CheckCircle2,
    title: 'Aktivasi Sistem',
    desc: 'Uji coba operasional dan sosialisasi kepada karyawan.',
  },
  {
    step: '5',
    icon: ShieldCheck,
    title: 'Sistem Berjalan',
    desc: 'Operasional lancar didampingi tim support teknis siap siaga.',
  },
];

export function RmihImplementationFlow() {
  return (
    <section className="py-20 bg-white dark:bg-slate-950 border-b border-slate-100 dark:border-slate-800">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-2xl mx-auto mb-16">
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">
            Alur Implementasi
          </h2>
          <p className="text-base text-slate-500 dark:text-slate-400">
            Proses yang jelas dan terstruktur untuk memastikan sistem berjalan dengan optimal.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-5 gap-4 relative">
          {STEPS.map((step, idx) => {
            const Icon = step.icon;
            return (
              <motion.div
                key={idx}
                initial={{ opacity: 0, y: 15 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: idx * 0.1 }}
                className="relative flex flex-col items-center text-center p-6 rounded-2xl bg-slate-50/70 dark:bg-slate-900/50 border border-slate-200/60 dark:border-slate-800 group hover:shadow-md transition-all"
              >
                {/* Step Circle with Number Badge */}
                <div className="relative mb-5">
                  <div className="w-14 h-14 rounded-full bg-blue-50 dark:bg-blue-950/80 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                    <Icon className="w-6 h-6" />
                  </div>
                  <span className="absolute -bottom-2 -right-1 w-6 h-6 rounded-full bg-blue-600 text-white font-black text-xs flex items-center justify-center shadow-md">
                    {step.step}
                  </span>
                </div>

                <h3 className="text-base font-bold text-slate-900 dark:text-white mb-2">
                  {step.title}
                </h3>
                <p className="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  {step.desc}
                </p>

                {/* Horizontal flow arrow connector for desktop */}
                {idx < STEPS.length - 1 && (
                  <div className="hidden md:block absolute -right-3 top-1/2 -translate-y-1/2 z-10">
                    <ChevronRight className="w-5 h-5 text-slate-300 dark:text-slate-700" />
                  </div>
                )}
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
