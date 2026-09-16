'use client';

import { motion } from 'framer-motion';
import { Users, Code, Cloud, ShieldCheck } from 'lucide-react';

const STATS = [
  { icon: Users, value: '21+', label: 'Fitur Lengkap' },
  { icon: Code, value: '4', label: 'Modul Utama' },
  { icon: Cloud, value: '1000+', label: 'Pengguna Aktif' },
  { icon: ShieldCheck, value: '99.9%', label: 'Keandalan Sistem' },
];

export function RmihStatsBanner() {
  return (
    <section className="relative py-14 overflow-hidden bg-gradient-to-r from-blue-700 via-blue-600 to-indigo-700 text-white shadow-inner">
      {/* Background decoration */}
      <div className="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(255,255,255,0.15),transparent_50%)] pointer-events-none" />

      <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
          {STATS.map((stat, idx) => {
            const Icon = stat.icon;
            return (
              <motion.div
                key={idx}
                initial={{ opacity: 0, y: 15 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.5, delay: idx * 0.1 }}
                className="flex flex-col items-center"
              >
                <div className="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center mb-3">
                  <Icon className="w-5 h-5 text-white" />
                </div>
                <div className="text-3xl sm:text-4xl font-black tracking-tight mb-1">
                  {stat.value}
                </div>
                <div className="text-xs sm:text-sm font-medium text-blue-100">
                  {stat.label}
                </div>
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
