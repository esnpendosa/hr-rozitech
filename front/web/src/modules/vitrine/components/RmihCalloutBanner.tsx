'use client';

import { Sparkles, Check, ArrowRight } from 'lucide-react';
import Link from 'next/link';

export function RmihCalloutBanner() {
  return (
    <section className="py-16 bg-slate-50/60 dark:bg-slate-900/40">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-8 sm:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none relative overflow-hidden">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div className="lg:col-span-8">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-300 text-xs font-semibold mb-4">
                <Sparkles className="w-3.5 h-3.5" />
                <span>Solusi Terbaik untuk Efisiensi Bisnis</span>
              </div>
              <h2 className="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-4">
                Siap meningkatkan efisiensi bisnis Anda?
              </h2>
              <p className="text-sm sm:text-base text-slate-600 dark:text-slate-400 mb-8 max-w-xl">
                Hubungi kami untuk mendapatkan penawaran terbaik dan konsultasi gratis sesuai kebutuhan operasional perusahaan Anda.
              </p>

              <div className="flex flex-wrap items-center gap-3.5">
                <Link
                  href="/contact"
                  className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-7 py-3.5 text-sm sm:text-base font-bold shadow-lg shadow-blue-600/25 transition-all hover:scale-105"
                >
                  <span>Hubungi Kami</span>
                  <ArrowRight className="w-4 h-4" />
                </Link>

                <Link
                  href="/pricing"
                  className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 px-6 py-3.5 text-sm sm:text-base font-semibold shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all"
                >
                  <span>Lihat Paket Harga</span>
                </Link>
              </div>
            </div>

            <div className="lg:col-span-4">
              <div className="rounded-2xl bg-blue-50/70 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 p-6 space-y-3.5">
                <div className="flex items-center gap-3 text-sm font-semibold text-slate-800 dark:text-slate-200">
                  <div className="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0">
                    <Check className="w-3.5 h-3.5" />
                  </div>
                  <span>Implementasi Cepat & Praktis</span>
                </div>
                <div className="flex items-center gap-3 text-sm font-semibold text-slate-800 dark:text-slate-200">
                  <div className="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0">
                    <Check className="w-3.5 h-3.5" />
                  </div>
                  <span>Harga Terjangkau & Transparan</span>
                </div>
                <div className="flex items-center gap-3 text-sm font-semibold text-slate-800 dark:text-slate-200">
                  <div className="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0">
                    <Check className="w-3.5 h-3.5" />
                  </div>
                  <span>Garansi & Pemeliharaan 1 Tahun</span>
                </div>
                <div className="flex items-center gap-3 text-sm font-semibold text-slate-800 dark:text-slate-200">
                  <div className="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0">
                    <Check className="w-3.5 h-3.5" />
                  </div>
                  <span>Tim Dukungan Profesional Siap Bantu</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
  );
}
