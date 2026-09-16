'use client';

import Image from 'next/image';
import { ShieldCheck, Server, Settings, Award, Headphones, ArrowRight } from 'lucide-react';
import Link from 'next/link';

export function RmihGuaranteeBanner() {
  return (
    <section className="py-12 bg-slate-950 text-white border-y border-slate-800">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="rounded-3xl bg-gradient-to-r from-slate-900 via-slate-950 to-blue-950 p-8 sm:p-12 border border-slate-800 relative overflow-hidden">
          
          <div className="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div className="lg:col-span-8">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-900/50 border border-blue-700/50 text-blue-300 text-xs font-semibold mb-4">
                <ShieldCheck className="w-3.5 h-3.5" />
                <span>Paket Lengkap Siap Pakai</span>
              </div>
              <h2 className="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight mb-4">
                Dapatkan semua fitur lengkap dengan <span className="text-blue-400">jaminan 1 tahun garansi & support.</span>
              </h2>

              <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-6 border-t border-slate-800">
                <div className="flex items-center gap-3">
                  <Server className="w-5 h-5 text-blue-400 shrink-0" />
                  <span className="text-xs font-medium text-slate-300">Cloud Server Siap Pakai</span>
                </div>
                <div className="flex items-center gap-3">
                  <Settings className="w-5 h-5 text-blue-400 shrink-0" />
                  <span className="text-xs font-medium text-slate-300">Konfigurasi & Instalasi</span>
                </div>
                <div className="flex items-center gap-3">
                  <Award className="w-5 h-5 text-blue-400 shrink-0" />
                  <span className="text-xs font-medium text-slate-300">Training Penggunaan</span>
                </div>
                <div className="flex items-center gap-3">
                  <Headphones className="w-5 h-5 text-blue-400 shrink-0" />
                  <span className="text-xs font-medium text-slate-300">Garansi & Support 1 Tahun</span>
                </div>
              </div>
            </div>

            <div className="lg:col-span-4 flex flex-col items-center lg:items-end gap-4">
              <div className="relative w-full max-w-sm aspect-[16/10] rounded-2xl overflow-hidden border border-slate-700 shadow-2xl bg-slate-900">
                <Image
                  src="/screenshots/rmih-hardware-bundle.jpg"
                  alt="RMIH Mini PC Server & Router Hardware Bundle"
                  fill
                  className="object-cover"
                />
              </div>
              <Link
                href="/contact"
                className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-7 py-3 text-sm font-bold shadow-xl shadow-blue-600/30 transition-all hover:scale-105"
              >
                <span>Hubungi Tim RMIH</span>
                <ArrowRight className="w-4 h-4" />
              </Link>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
