'use client';

import React, { useState } from 'react';
import { MessageCircle, X, Send } from 'lucide-react';

export function WhatsAppWidget() {
  const [isOpen, setIsOpen] = useState(false);
  const phoneNumber = '6281234567890'; // Representative WhatsApp Business
  const defaultMessage = encodeURIComponent('Halo Tim RMIH, saya tertarik untuk konsultasi implementasi HR & Penggajian untuk perusahaan saya.');

  const handleOpenWhatsApp = () => {
    window.open(`https://wa.me/${phoneNumber}?text=${defaultMessage}`, '_blank', 'noopener,noreferrer');
  };

  return (
    <div className="fixed bottom-6 right-6 z-50 flex flex-col items-end">
      {/* Pop-up dialog */}
      {isOpen && (
        <div className="mb-4 w-80 rounded-2xl bg-white p-5 shadow-2xl ring-1 ring-slate-900/10 transition-all animate-fade-in dark:bg-slate-900 dark:ring-slate-800">
          <div className="flex items-center justify-between border-b border-slate-100 pb-3 dark:border-slate-800">
            <div className="flex items-center gap-3">
              <div className="relative">
                <div className="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-500 text-white shadow-md shadow-emerald-500/30">
                  <MessageCircle className="h-5 w-5 fill-current" />
                </div>
                <span className="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-emerald-400 dark:border-slate-900" />
              </div>
              <div>
                <h4 className="text-sm font-bold text-slate-900 dark:text-white">Konsultasi RMIH</h4>
                <p className="text-[11px] font-medium text-emerald-600 dark:text-emerald-400">Tim kami siap membantu</p>
              </div>
            </div>
            <button
              onClick={() => setIsOpen(false)}
              className="rounded-lg p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-600 dark:hover:bg-slate-800"
              aria-label="Tutup"
            >
              <X className="h-4 w-4" />
            </button>
          </div>

          <div className="my-4 rounded-xl bg-slate-50 p-3.5 text-xs text-slate-600 dark:bg-slate-800/60 dark:text-slate-300 leading-relaxed">
            👋 Halo! Butuh info demo produk, simulasi harga, atau integrasi ZKTeco & PPh 21 untuk tim Anda?
          </div>

          <button
            onClick={handleOpenWhatsApp}
            className="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-500 py-3 text-xs font-bold text-white shadow-lg shadow-emerald-500/25 transition-all hover:bg-emerald-600 hover:shadow-emerald-500/40"
          >
            <Send className="h-3.5 w-3.5" />
            Chat WhatsApp Sekarang
          </button>
        </div>
      )}

      {/* Floating Trigger Button */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="group flex items-center gap-2.5 rounded-full bg-emerald-500 px-4 py-3.5 text-white shadow-xl shadow-emerald-500/30 transition-all duration-300 hover:scale-105 hover:bg-emerald-600 hover:shadow-emerald-500/50"
        aria-label="WhatsApp Kami"
      >
        <div className="relative flex items-center justify-center">
          <MessageCircle className="h-6 w-6 fill-current" />
          <span className="absolute -top-1 -right-1 flex h-3 w-3">
            <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-200 opacity-75"></span>
            <span className="relative inline-flex h-3 w-3 rounded-full bg-white"></span>
          </span>
        </div>
        <span className="text-sm font-bold tracking-tight pr-1">WhatsApp kami</span>
      </button>
    </div>
  );
}
