'use client';

import { Sparkles, Check, ArrowRight } from 'lucide-react';
import Link from 'next/link';

export function RmihCalloutBanner() {
  return (
    <section className="py-12 bg-white dark:bg-slate-950">
      <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-10 shadow-sm relative overflow-hidden">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div className="lg:col-span-8">
              <div className="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800 text-blue-600 dark:text-blue-400 text-[11px] font-semibold mb-3">
                <Sparkles className="w-3 h-3" />
                <span>Solusi Terbaik untuk Efisiensi Bisnis</span>
              </div>
              <h2 className="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-3">
                Siap meningkatkan efisiensi bisnis Anda?
              </h2>
              <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-lg leading-relaxed">
                Hubungi kami untuk mendapatkan penawaran terbaik dan konsultasi gratis sesuai kebutuhan operasional perusahaan Anda.
              </p>

              <div className="flex flex-wrap items-center gap-3">
                <Link
                  href="/contact"
                  className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 text-xs sm:text-sm font-bold shadow-sm transition-all"
                >
                  <span>Hubungi Kami</span>
                  <ArrowRight className="w-3.5 h-3.5" />
                </Link>

                <Link
                  href="/pricing"
                  className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 px-5 py-2.5 text-xs sm:text-sm font-semibold shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all"
                >
                  <span>Lihat Paket Harga</span>
                </Link>
              </div>
            </div>

            <div className="lg:col-span-4">
              <div className="rounded-xl bg-blue-50/60 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/40 p-5 space-y-2.5">
                <div className="flex items-center gap-2.5 text-xs font-semibold text-slate-800 dark:text-slate-200">
                  <div className="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <Check className="w-2.5 h-2.5" />
                  </div>
                  <span>Implementasi Cepat & Praktis</span>
                </div>
                <div className="flex items-center gap-2.5 text-xs font-semibold text-slate-800 dark:text-slate-200">
                  <div className="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <Check className="w-2.5 h-2.5" />
                  </div>
                  <span>Harga Terjangkau & Transparan</span>
                </div>
                <div className="flex items-center gap-2.5 text-xs font-semibold text-slate-800 dark:text-slate-200">
                  <div className="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <Check className="w-2.5 h-2.5" />
                  </div>
                  <span>Garansi & Pemeliharaan 1 Tahun</span>
                </div>
                <div className="flex items-center gap-2.5 text-xs font-semibold text-slate-800 dark:text-slate-200">
                  <div className="w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <Check className="w-2.5 h-2.5" />
                  </div>
                  <span>Tim Dukungan Profesional</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </section>
  );
}
