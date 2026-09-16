'use client';

import { UserCheck, Search, Wrench, CheckCircle2, ShieldCheck, ChevronRight } from 'lucide-react';

const STEPS = [
  {
    step: '1',
    icon: UserCheck,
    title: 'Registrasi Klien',
    desc: 'Pengisian data awal dan kontrak',
  },
  {
    step: '2',
    icon: Search,
    title: 'Survey Lokasi',
    desc: 'Analisis jaringan dan kebutuhan',
  },
  {
    step: '3',
    icon: Wrench,
    title: 'Instalasi',
    desc: 'Pemasangan perangkat dan konfigurasi',
  },
  {
    step: '4',
    icon: CheckCircle2,
    title: 'Aktivasi OLT/ONU',
    desc: 'Pengujian koneksi dan layanan',
  },
  {
    step: '5',
    icon: ShieldCheck,
    title: 'Sistem Prepaid',
    desc: 'Integrasi pembayaran dan monitoring',
  },
];

export function RmihImplementationFlow() {
  return (
    <section className="py-16 bg-white dark:bg-slate-950">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-xl mx-auto mb-12">
          <h2 className="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">
            Alur Implementasi
          </h2>
          <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
            Proses yang jelas dan terstruktur untuk memastikan sistem berjalan dengan optimal.
          </p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 relative">
          {STEPS.map((step, idx) => {
            const Icon = step.icon;
            return (
              <div
                key={idx}
                className="relative flex flex-col items-center text-center p-4 rounded-xl bg-slate-50/60 dark:bg-slate-900/40 border border-slate-200/70 dark:border-slate-800"
              >
                <div className="relative mb-3">
                  <div className="w-12 h-12 rounded-full bg-blue-50 dark:bg-blue-950/80 border border-blue-200 dark:border-blue-800 flex items-center justify-center text-blue-600 dark:text-blue-400">
                    <Icon className="w-5 h-5" />
                  </div>
                  <span className="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-blue-600 text-white font-black text-[10px] flex items-center justify-center shadow-sm">
                    {step.step}
                  </span>
                </div>

                <h3 className="text-xs font-bold text-slate-900 dark:text-white mb-1">
                  {step.title}
                </h3>
                <p className="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                  {step.desc}
                </p>

                {idx < STEPS.length - 1 && (
                  <div className="hidden md:block absolute -right-2 top-1/2 -translate-y-1/2 z-10">
                    <ChevronRight className="w-4 h-4 text-slate-300 dark:text-slate-700" />
                  </div>
                )}
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
