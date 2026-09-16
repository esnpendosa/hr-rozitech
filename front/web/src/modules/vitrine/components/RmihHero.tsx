'use client';

import Image from 'next/image';
import Link from 'next/link';
import { motion } from 'framer-motion';
import { CheckCircle2, ArrowRight, Play, Sparkles } from 'lucide-react';
import { useSearchParams } from 'next/navigation';
import { withLocaleHref } from '../lib/locale-href';

export interface RmihHeroProps {
  badge?: string;
  headlineHighlight?: string;
  headlinePrefix?: string;
  headlineSuffix?: string;
  subheadline?: string;
  benefits?: string[];
  primaryCtaText?: string;
  primaryCtaHref?: string;
  secondaryCtaText?: string;
  secondaryCtaHref?: string;
  floatingBadgeText?: string;
}

export function RmihHero({
  badge = 'Platform Software Manajemen Terpadu',
  headlinePrefix = 'Ekosistem',
  headlineHighlight = 'software',
  headlineSuffix = 'terpadu untuk mendukung pertumbuhan bisnis profesional',
  subheadline = 'RMIH (Resources Management Integrated Human) adalah solusi lengkap untuk mengelola karyawan, absensi, operasional, dan HR bisnis Anda dalam satu platform yang mudah digunakan.',
  benefits = ['Lebih Efisien', 'Lebih Terorganisir', 'Siap untuk Masa Depan'],
  primaryCtaText = 'Mulai Sekarang',
  primaryCtaHref = '/signup',
  secondaryCtaText = 'Lihat Demo',
  secondaryCtaHref = '#detail-fitur',
  floatingBadgeText = 'Kelola bisnis Anda, lebih mudah!',
}: RmihHeroProps) {
  const searchParams = useSearchParams();
  const search = searchParams ? searchParams.toString() : '';

  return (
    <section className="relative min-h-[88dvh] flex items-center justify-center overflow-hidden pt-28 pb-16 lg:pt-32 lg:pb-20 bg-gradient-to-b from-blue-50/50 via-white to-white dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
      {/* Background Soft Glows */}
      <div className="absolute top-10 left-1/4 w-96 h-96 bg-blue-400/10 dark:bg-blue-600/10 rounded-full blur-3xl pointer-events-none" />
      <div className="absolute top-1/3 right-10 w-96 h-96 bg-indigo-400/10 dark:bg-indigo-600/10 rounded-full blur-3xl pointer-events-none" />

      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
          
          {/* Left Column: Copywriting & Actions */}
          <div className="lg:col-span-6 text-center lg:text-left">
            {/* Top Pill Badge */}
            {badge && (
              <motion.div
                initial={{ opacity: 0, y: 15 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ duration: 0.5 }}
                className="inline-flex items-center gap-2 rounded-full border border-blue-200 dark:border-blue-800 bg-blue-50/80 dark:bg-blue-950/60 px-3.5 py-1.5 text-xs font-semibold text-blue-700 dark:text-blue-300 shadow-sm mb-6 backdrop-blur-sm"
              >
                <Sparkles className="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
                <span>{badge}</span>
              </motion.div>
            )}

            {/* Main Headline */}
            <motion.h1
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.1 }}
              className="text-3xl sm:text-4xl lg:text-[2.75rem] font-black tracking-tight text-slate-900 dark:text-white leading-[1.2] mb-5 text-balance"
            >
              {headlinePrefix}{' '}
              <span className="text-blue-600 dark:text-blue-400">{headlineHighlight}</span>{' '}
              {headlineSuffix}
            </motion.h1>

            {/* Subheadline description */}
            <motion.p
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.2 }}
              className="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed mb-7 max-w-xl mx-auto lg:mx-0"
            >
              {subheadline}
            </motion.p>

            {/* 3 Value Points (Pills with Checkmark) */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.3 }}
              className="flex flex-wrap items-center justify-center lg:justify-start gap-3 mb-8"
            >
              {benefits.map((benefit, idx) => (
                <div
                  key={idx}
                  className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs sm:text-sm font-semibold text-slate-700 dark:text-slate-200 shadow-sm"
                >
                  <CheckCircle2 className="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0" />
                  <span>{benefit}</span>
                </div>
              ))}
            </motion.div>

            {/* Action Buttons */}
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.6, delay: 0.4 }}
              className="flex flex-wrap items-center justify-center lg:justify-start gap-3.5"
            >
              <Link
                href={withLocaleHref(primaryCtaHref, search)}
                className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-7 py-3.5 text-sm sm:text-base font-bold shadow-lg shadow-blue-600/25 transition-all hover:scale-[1.02] active:scale-[0.98]"
              >
                <span>{primaryCtaText}</span>
                <ArrowRight className="w-4 h-4" />
              </Link>

              <Link
                href={withLocaleHref(secondaryCtaHref, search)}
                className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 px-6 py-3.5 text-sm sm:text-base font-semibold shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all hover:scale-[1.02]"
              >
                <Play className="w-4 h-4 text-blue-600 dark:text-blue-400 fill-current" />
                <span>{secondaryCtaText}</span>
              </Link>
            </motion.div>
          </div>

          {/* Right Column: Visual Composite (Web Browser Mockup + Floating Mobile App Mockup) */}
          <motion.div
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.8, delay: 0.2 }}
            className="lg:col-span-6 relative flex items-center justify-center"
          >
            {/* Playful Handwritten Floating Badge */}
            {floatingBadgeText && (
              <div className="absolute -top-6 right-4 sm:right-8 z-20 hidden sm:flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-blue-600 text-white text-xs font-bold shadow-lg shadow-blue-500/20 rotate-[-4deg]">
                <span>{floatingBadgeText}</span>
              </div>
            )}

            {/* Desktop Web Dashboard Frame */}
            <div className="relative w-full max-w-lg lg:max-w-none rounded-2xl sm:rounded-3xl overflow-hidden border border-slate-200/90 dark:border-slate-800 shadow-2xl bg-white dark:bg-slate-900">
              {/* Browser Header Bar */}
              <div className="flex items-center justify-between h-9 px-4 bg-slate-100 dark:bg-slate-800/80 border-b border-slate-200/70 dark:border-slate-700/60">
                <div className="flex items-center gap-1.5">
                  <span className="w-2.5 h-2.5 rounded-full bg-red-400" />
                  <span className="w-2.5 h-2.5 rounded-full bg-amber-400" />
                  <span className="w-2.5 h-2.5 rounded-full bg-emerald-400" />
                </div>
                <div className="text-[11px] font-medium text-slate-400">app.rmih.id/dashboard</div>
                <div className="w-10" />
              </div>

              {/* Dashboard Image */}
              <div className="relative aspect-[16/10] bg-slate-100 dark:bg-slate-950 overflow-hidden">
                <Image
                  src="/screenshots/web-dashboard.png"
                  alt="RMIH Web Dashboard"
                  fill
                  priority
                  sizes="(max-width: 1024px) 100vw, 50vw"
                  className="object-cover object-top"
                />
              </div>
            </div>

            {/* Floating Mobile Phone Frame on Bottom Left */}
            <div className="absolute -bottom-8 -left-4 sm:-left-8 z-10 w-36 sm:w-48 rounded-[2rem] p-1.5 bg-slate-900 border-2 border-slate-700 shadow-2xl shadow-blue-900/30 overflow-hidden">
              <div className="relative aspect-[9/19] rounded-[1.6rem] overflow-hidden bg-slate-950">
                <Image
                  src="/screenshots/mobile-attendance.png"
                  alt="RMIH Mobile App"
                  fill
                  sizes="(max-width: 640px) 140px, 192px"
                  className="object-cover object-top"
                />
              </div>
            </div>
          </motion.div>

        </div>
      </div>
    </section>
  );
}
