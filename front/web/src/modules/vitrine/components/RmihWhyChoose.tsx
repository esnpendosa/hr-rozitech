'use client';

import { Rocket, ShieldCheck, Headphones, Sliders } from 'lucide-react';

const WHY_POINTS = [
  {
    icon: Rocket,
    title: 'Mudah Digunakan',
    description: 'Interface yang intuitif dan responsif di semua perangkat.',
    color: 'text-blue-600',
    bgColor: 'bg-blue-50 dark:bg-blue-950/60',
  },
  {
    icon: ShieldCheck,
    title: 'Keamanan Terjamin',
    description: 'Keamanan data dan jaringan data selalu aman.',
    color: 'text-blue-600',
    bgColor: 'bg-blue-50 dark:bg-blue-950/60',
  },
  {
    icon: Headphones,
    title: 'Dukungan Penuh',
    description: 'Training, instalasi, dan support 1 tahun penuh.',
    color: 'text-blue-600',
    bgColor: 'bg-blue-50 dark:bg-blue-950/60',
  },
  {
    icon: Sliders,
    title: 'Fleksibel & Skalabel',
    description: 'Cocok untuk bisnis skala kecil hingga besar.',
    color: 'text-blue-600',
    bgColor: 'bg-blue-50 dark:bg-blue-950/60',
  },
];

export function RmihWhyChoose() {
  return (
    <section className="py-16 bg-white dark:bg-slate-950">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-xl mx-auto mb-14">
          <h2 className="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">
            Mengapa Memilih RMIH?
          </h2>
          <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
            Solusi lengkap yang dirancang khusus untuk kebutuhan bisnis modern.
          </p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {WHY_POINTS.map((point, idx) => {
            const Icon = point.icon;
            return (
              <div
                key={idx}
                className="text-center flex flex-col items-center p-4 rounded-xl transition-all"
              >
                <div className={`w-12 h-12 rounded-2xl ${point.bgColor} flex items-center justify-center mb-4`}>
                  <Icon className={`w-6 h-6 ${point.color}`} />
                </div>
                <h3 className="text-base font-bold text-slate-900 dark:text-white mb-1.5">
                  {point.title}
                </h3>
                <p className="text-xs text-slate-500 dark:text-slate-400 leading-relaxed max-w-[200px]">
                  {point.description}
                </p>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
