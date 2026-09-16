'use client';

import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Plus, Minus } from 'lucide-react';

const FAQ_LIST = [
  {
    question: 'Metode absensi apa saja yang didukung?',
    answer: 'RMIH mendukung mesin absensi biometrik ZKTeco (sidik jari & wajah), absensi mobile GPS dengan foto selfie & anti-fake GPS, QR Code dinamis, dan portal absensi kiosk.',
  },
  {
    question: 'Apakah data karyawan dan perusahaan kami aman?',
    answer: 'Sangat aman. Seluruh data disimpan dengan enkripsi standar industri (AES-256), transfer data terlindungi TLS 1.3, backup berkala, dan akses berbasis peran (RBAC).',
  },
  {
    question: 'Dapatkah kami memindahkan data dari sistem lama atau Excel?',
    answer: 'Tentu bisa. Tim kami menyediakan template migrasi data yang mudah dan siap membantu mengimpor seluruh data master karyawan dari Excel ke RMIH.',
  },
  {
    question: 'Apakah ada kontrak minimal berlangganan?',
    answer: 'Tidak ada batasan kaku. Anda dapat memilih paket bulanan yang fleksibel atau paket tahunan dengan diskon khusus dan pendampingan implementasi prioritas.',
  },
];

export function RmihFAQ() {
  const [openIndex, setOpenIndex] = useState<number | null>(null);

  return (
    <section className="py-20 bg-white dark:bg-slate-950">
      <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {/* Header - Simple clean font matching image 1 */}
        <div className="text-center mb-12">
          <h2 className="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mb-2">
            Pertanyaan yang Sering Diajukan
          </h2>
          <p className="text-xs sm:text-sm text-slate-500 dark:text-slate-400">
            Temukan jawaban untuk pertanyaan yang paling umum.
          </p>
        </div>

        {/* FAQ List */}
        <div className="space-y-3">
          {FAQ_LIST.map((faq, idx) => {
            const isOpen = openIndex === idx;
            return (
              <div
                key={idx}
                className="rounded-xl border border-slate-200/90 dark:border-slate-800 bg-white dark:bg-slate-900 overflow-hidden transition-all"
              >
                <button
                  type="button"
                  onClick={() => setOpenIndex(isOpen ? null : idx)}
                  className="w-full flex items-center justify-between p-4 sm:p-5 text-left text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                >
                  <span>{faq.question}</span>
                  <div className="w-6 h-6 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 ml-4">
                    {isOpen ? <Minus className="w-3.5 h-3.5" /> : <Plus className="w-3.5 h-3.5" />}
                  </div>
                </button>

                <AnimatePresence>
                  {isOpen && (
                    <motion.div
                      initial={{ height: 0, opacity: 0 }}
                      animate={{ height: 'auto', opacity: 1 }}
                      exit={{ height: 0, opacity: 0 }}
                      transition={{ duration: 0.2 }}
                    >
                      <div className="px-5 pb-5 pt-1 text-xs text-slate-600 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-800/60">
                        {faq.answer}
                      </div>
                    </motion.div>
                  )}
                </AnimatePresence>
              </div>
            );
          })}
        </div>

      </div>
    </section>
  );
}
