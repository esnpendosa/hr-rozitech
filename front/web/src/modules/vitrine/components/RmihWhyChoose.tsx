'use client';

import { motion } from 'framer-motion';
import { Rocket, ShieldCheck, Headphones, Sliders } from 'lucide-react';

const WHY_POINTS = [
  {
    icon: Rocket,
    title: 'Mudah Digunakan',
    description: 'Interface yang intuitif dan responsif di semua perangkat (Web & Mobile).',
    color: 'text-blue-600 dark:text-blue-400',
    bgColor: 'bg-blue-50 dark:bg-blue-950/60',
  },
  {
    icon: ShieldCheck,
    title: 'Keamanan Terjamin',
    description: 'Data karyawan, penggajian, dan log absensi tersimpan terenkripsi aman.',
    color: 'text-blue-600 dark:text-blue-400',
    bgColor: 'bg-blue-50 dark:bg-blue-950/60',
  },
  {
    icon: Headphones,
    title: 'Dukungan Penuh',
    description: 'Panduan, training instalasi, dan support teknis siap membantu perusahaan Anda.',
    color: 'text-blue-600 dark:text-blue-400',
    bgColor: 'bg-blue-50 dark:bg-blue-950/60',
  },
  {
    icon: Sliders,
    title: 'Fleksibel & Skalabel',
    description: 'Cocok untuk skala UMKM, bisnis multi-cabang, hingga perusahaan besar.',
    color: 'text-blue-600 dark:text-blue-400',
    bgColor: 'bg-blue-50 dark:bg-blue-950/60',
  },
];

export function RmihWhyChoose() {
  return (
    <section className="py-20 bg-white dark:bg-slate-950 border-t border-slate-100 dark:border-slate-800/60">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center max-w-2xl mx-auto mb-16">
          <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">
            Mengapa Memilih RMIH?
          </h2>
          <p className="text-base text-slate-500 dark:text-slate-400">
            Solusi lengkap yang dirancang khusus untuk kebutuhan operasional & HR modern.
          </p>
        </div>

        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          {WHY_POINTS.map((point, idx) => {
            const Icon = point.icon;
            return (
              <motion.div
                key={idx}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: idx * 0.1 }}
                className="text-center flex flex-col items-center p-6 rounded-2xl bg-slate-50/70 dark:bg-slate-900/40 border border-slate-200/60 dark:border-slate-800/50 hover:shadow-md transition-all group"
              >
                <div className={`w-14 h-14 rounded-2xl ${point.bgColor} flex items-center justify-center mb-5 group-hover:scale-110 transition-transform`}>
                  <Icon className={`w-7 h-7 ${point.color}`} />
                </div>
                <h3 className="text-lg font-bold text-slate-900 dark:text-white mb-2">
                  {point.title}
                </h3>
                <p className="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                  {point.description}
                </p>
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
