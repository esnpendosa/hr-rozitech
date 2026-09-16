'use client';

import Image from 'next/image';
import Link from 'next/link';
import { motion } from 'framer-motion';
import { Check, ArrowRight, Play, Sparkles } from 'lucide-react';
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
  badge = 'Platform Management HR Terlengkap',
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
    <section className="relative min-h-[85dvh] flex items-center justify-center overflow-hidden pt-24 pb-16 lg:pt-28 lg:pb-20 bg-gradient-to-b from-blue-50/40 via-white to-white dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
      <div className="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-8 items-center">
          
          {/* Left Column: Copywriting & Actions */}
          <div className="lg:col-span-6 text-center lg:text-left">
            {/* Top Pill Badge */}
            {badge && (
              <div className="inline-flex items-center gap-2 rounded-full border border-blue-200 dark:border-blue-800 bg-blue-50/80 dark:bg-blue-950/60 px-3 py-1 text-xs font-semibold text-blue-600 dark:text-blue-400 shadow-sm mb-5 backdrop-blur-sm">
                <Sparkles className="w-3.5 h-3.5" />
                <span>{badge}</span>
              </div>
            )}

            {/* Main Headline */}
            <h1 className="text-3xl sm:text-4xl lg:text-[2.65rem] font-black tracking-tight text-slate-900 dark:text-white leading-[1.22] mb-4 text-balance">
              {headlinePrefix}{' '}
              <span className="text-blue-600 dark:text-blue-500">{headlineHighlight}</span>{' '}
              {headlineSuffix}
            </h1>

            {/* Subheadline description */}
            <p className="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed mb-6 max-w-xl mx-auto lg:mx-0 font-normal">
              {subheadline}
            </p>

            {/* 3 Value Points (Pills with small blue checkmark circle) */}
            <div className="flex flex-wrap items-center justify-center lg:justify-start gap-2.5 mb-8">
              {benefits.map((benefit, idx) => (
                <div
                  key={idx}
                  className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-medium text-slate-700 dark:text-slate-200 shadow-sm"
                >
                  <div className="w-4 h-4 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0">
                    <Check className="w-2.5 h-2.5" />
                  </div>
                  <span>{benefit}</span>
                </div>
              ))}
            </div>

            {/* Action Buttons */}
            <div className="flex flex-wrap items-center justify-center lg:justify-start gap-3.5">
              <Link
                href={withLocaleHref(primaryCtaHref, search)}
                className="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 text-sm font-bold shadow-md shadow-blue-600/20 transition-all hover:scale-[1.02] active:scale-[0.98]"
              >
                <span>{primaryCtaText}</span>
                <ArrowRight className="w-4 h-4" />
              </Link>

              <Link
                href={withLocaleHref(secondaryCtaHref, search)}
                className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 px-5 py-3 text-sm font-semibold shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-all hover:scale-[1.02]"
              >
                <Play className="w-3.5 h-3.5 text-blue-600 dark:text-blue-400 fill-current" />
                <span>{secondaryCtaText}</span>
              </Link>
            </div>
          </div>

          {/* Right Column: Visual Composite (Web Browser Mockup + Floating Mobile App Mockup) */}
          <div className="lg:col-span-6 relative flex items-center justify-center mt-6 lg:mt-0">
            {/* Playful Handwritten Floating Badge */}
            {floatingBadgeText && (
              <div className="absolute -top-5 right-6 z-20 hidden sm:flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-600 text-white text-[11px] font-bold shadow-lg shadow-blue-500/20 rotate-[-4deg]">
                <span>{floatingBadgeText}</span>
              </div>
            )}

            {/* Desktop Web Dashboard Frame */}
            <div className="relative w-full max-w-lg lg:max-w-none rounded-2xl overflow-hidden border border-slate-200/90 dark:border-slate-800 shadow-2xl bg-white dark:bg-slate-900">
              {/* Browser Header Bar */}
              <div className="flex items-center justify-between h-8 px-4 bg-slate-100 dark:bg-slate-800/80 border-b border-slate-200/70 dark:border-slate-700/60">
                <div className="flex items-center gap-1.5">
                  <span className="w-2.5 h-2.5 rounded-full bg-red-400" />
                  <span className="w-2.5 h-2.5 rounded-full bg-amber-400" />
                  <span className="w-2.5 h-2.5 rounded-full bg-emerald-400" />
                </div>
                <div className="text-[10px] font-medium text-slate-400">app.rmih.id/dashboard</div>
                <div className="w-8" />
              </div>

              {/* Dashboard Image */}
              <div className="relative aspect-[16/10] bg-slate-100 dark:bg-slate-950 overflow-hidden">
                <Image
                  src="/screenshots/rmih-dashboard.jpg"
                  alt="RMIH Web Dashboard"
                  fill
                  priority
                  sizes="(max-width: 1024px) 100vw, 50vw"
                  className="object-cover object-top"
                />
              </div>
            </div>

            {/* Floating Mobile Phone Frame on Bottom Left */}
            <div className="absolute -bottom-6 -left-3 sm:-left-6 z-10 w-36 sm:w-44 rounded-[1.8rem] p-1 bg-slate-900 border-2 border-slate-700 shadow-2xl shadow-blue-900/30 overflow-hidden">
              <div className="relative aspect-[9/19] rounded-[1.5rem] overflow-hidden bg-slate-950">
                <Image
                  src="/screenshots/rmih-mobile.jpg"
                  alt="RMIH Mobile App"
                  fill
                  sizes="(max-width: 640px) 140px, 176px"
                  className="object-cover object-top"
                />
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>
  );
}
