'use client';

import Link from 'next/link';
import { motion, useScroll, useTransform, useSpring } from 'framer-motion';
import { ArrowRight, ChevronDown, Play, Sparkles } from 'lucide-react';
import { useRef } from 'react';
import { useSearchParams } from 'next/navigation';
import { withLocaleHref } from '../../lib/locale-href';
import { ParticleField } from '../ParticleField';

export interface HeroSectionProps {
  headline: string;
  subheadline: string;
  badge?: string | {
    icon?: React.ReactNode;
    text: string;
    label?: string;
  };
  ctaPrimary?: {
    text: string;
    href: string;
    icon?: React.ReactNode;
  };
  ctaSecondary?: {
    text: string;
    href: string;
    icon?: React.ReactNode;
  };
  visual?: React.ReactNode;
  stats?: Array<{
    value: number;
    suffix: string;
    label: string;
    icon?: React.ReactNode;
  }>;
  animated?: boolean;
  layout?: 'centered' | 'split';
  quickTrialForm?: React.ReactNode;
  }

export function HeroSection({
  headline,
  subheadline,
  badge,
  ctaPrimary,
  ctaSecondary,
  visual,
  stats,
  animated = true,
  layout = 'centered',
  quickTrialForm,
  }: HeroSectionProps) {
  const ref = useRef<HTMLElement>(null);
  const { scrollYProgress } = useScroll({ target: ref, offset: ['start start', 'end start'] });
  const y = useSpring(useTransform(scrollYProgress, [0, 1], [0, -180]), { stiffness: 80, damping: 30 });
  const opacity = useTransform(scrollYProgress, [0, 0.6], [1, 0]);
  const scale = useTransform(scrollYProgress, [0, 0.6], [1, 0.95]);
  const searchParams = useSearchParams();
  const search = searchParams.toString();
  const badgeConfig = typeof badge === 'string' ? { text: badge } : badge;

  const isSplit = layout === 'split' && Boolean(visual);
  const align = isSplit ? 'text-center lg:text-left' : 'text-center';

  return (
    <section ref={ref} className="relative min-h-[92dvh] flex items-center justify-center overflow-hidden pt-28 pb-20">
      {/* Background ambient lighting matching high-end corporate style (Mekari reference) */}
      <div className="absolute inset-0 bg-gradient-to-b from-white via-slate-50/40 to-white dark:from-slate-950 dark:via-slate-950/80 dark:to-slate-950 pointer-events-none" />
      
      {/* Soft ambient violet/indigo mesh glow at top & bottom-left */}
      <div className="absolute -top-32 left-1/2 -translate-x-1/2 w-[850px] h-[450px] bg-gradient-to-b from-indigo-200/30 via-blue-100/20 to-transparent dark:from-indigo-950/35 dark:via-blue-950/20 dark:to-transparent rounded-full blur-3xl pointer-events-none" />
      <div className="absolute bottom-10 -left-20 w-[400px] h-[400px] bg-purple-500/10 dark:bg-purple-900/15 rounded-full blur-[100px] pointer-events-none" />
      <div className="absolute top-1/3 -right-20 w-[400px] h-[400px] bg-blue-500/10 dark:bg-blue-900/15 rounded-full blur-[100px] pointer-events-none" />

      {/* Subtle modern corporate grid */}
      <div
        className="absolute inset-0 opacity-[0.025] dark:opacity-[0.04] pointer-events-none"
        style={{
          backgroundImage: 'linear-gradient(rgba(0,0,0,0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.08) 1px, transparent 1px)',
          backgroundSize: '48px 48px',
        }}
      />

      {animated && <ParticleField />}

      <motion.div
        style={animated ? { y, opacity, scale } : {}}
        className={`relative z-10 mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 ${
          isSplit ? 'pt-16 pb-16 lg:pt-24' : 'pt-12 pb-16'
        }`}
      >
        <div
          className={
            isSplit
              ? 'grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-12 xl:gap-16'
              : 'mx-auto max-w-4xl text-center'
          }
        >
          {/* ── Main Content Area ─────────────────────────────────────── */}
          <div className={align}>
            {/* Top Pill Badge */}
            {badgeConfig && (
              <motion.div
                initial={animated ? { opacity: 0, y: 16, filter: 'blur(8px)' } : {}}
                animate={animated ? { opacity: 1, y: 0, filter: 'blur(0px)' } : {}}
                transition={{ duration: 0.7 }}
                className="mb-6 inline-flex items-center gap-2 rounded-full border border-indigo-200/80 dark:border-indigo-800/60 bg-indigo-50/80 dark:bg-indigo-950/50 px-4 py-1.5 text-xs sm:text-sm font-semibold text-indigo-700 dark:text-indigo-300 shadow-sm backdrop-blur-sm"
              >
                {badgeConfig.icon && <span className="text-indigo-600 dark:text-indigo-400">{badgeConfig.icon}</span>}
                <span>{badgeConfig.text}</span>
                {badgeConfig.label && (
                  <span className="rounded-full bg-indigo-600 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-white">
                    {badgeConfig.label}
                  </span>
                )}
              </motion.div>
            )}

            {/* Corporate Headline (Balanced typography) */}
            <motion.h1
              initial={animated ? { opacity: 0, y: 24 } : {}}
              animate={animated ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.85, delay: 0.1, ease: [0.22, 1, 0.36, 1] }}
              className={`mb-6 text-balance font-extrabold sm:font-black tracking-tight text-slate-900 dark:text-white ${
                isSplit
                  ? 'text-3xl sm:text-4xl lg:text-5xl leading-[1.15]'
                  : 'text-3xl sm:text-4xl md:text-5xl lg:text-[3.4rem] leading-[1.18]'
              }`}
            >
              {headline}
            </motion.h1>

            {/* Subtitle */}
            <motion.p
              initial={animated ? { opacity: 0, y: 16 } : {}}
              animate={animated ? { opacity: 1, y: 0 } : {}}
              transition={{ duration: 0.75, delay: 0.25 }}
              className={`leading-relaxed text-slate-600 dark:text-slate-400 ${
                isSplit
                  ? 'mb-8 text-base sm:text-lg lg:max-w-xl'
                  : 'mx-auto mb-10 max-w-2xl text-base sm:text-lg sm:leading-relaxed'
              }`}
            >
              {subheadline}
            </motion.p>

            {/* Centered Dual CTAs */}
            {(ctaPrimary || ctaSecondary) && (
              <motion.div
                initial={animated ? { opacity: 0, y: 18 } : {}}
                animate={animated ? { opacity: 1, y: 0 } : {}}
                transition={{ duration: 0.75, delay: 0.38 }}
                className={`flex flex-wrap items-center gap-3.5 ${
                  isSplit ? 'justify-center lg:justify-start' : 'justify-center'
                }`}
              >
                {ctaPrimary && (
                  <Link
                    href={withLocaleHref(ctaPrimary.href, search)}
                    className="inline-flex items-center justify-center gap-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-7 py-3.5 text-sm sm:text-base font-bold shadow-lg shadow-blue-600/25 transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]"
                  >
                    <span>{ctaPrimary.text}</span>
                    {ctaPrimary.icon || <ArrowRight className="w-4 h-4" />}
                  </Link>
                )}

                {ctaSecondary && (
                  <Link
                    href={withLocaleHref(ctaSecondary.href, search)}
                    className="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200/90 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 text-slate-800 dark:text-slate-200 px-6 py-3.5 text-sm sm:text-base font-semibold shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all duration-200"
                  >
                    <span>{ctaSecondary.text}</span>
                    {ctaSecondary.icon || <ChevronDown className="w-4 h-4 text-slate-500 dark:text-slate-400" />}
                  </Link>
                )}
              </motion.div>
            )}

            {/* Optional Quick Trial Email Form (if rendered in centered mode) */}
            {quickTrialForm}


          </div>

          {/* ── Visual (When Split) ─────────────────────────────────── */}
          {isSplit && visual && (
            <motion.div
              initial={animated ? { opacity: 0, x: 30, scale: 0.98 } : {}}
              animate={animated ? { opacity: 1, x: 0, scale: 1 } : {}}
              transition={{ duration: 0.9, delay: 0.35, ease: [0.22, 1, 0.36, 1] }}
              className="w-full"
            >
              {visual}
            </motion.div>
          )}
        </div>

        {/* Bottom Key Metric Stats */}
        {stats && stats.length > 0 && (
          <motion.div
            initial={animated ? { opacity: 0, y: 24 } : {}}
            animate={animated ? { opacity: 1, y: 0 } : {}}
            transition={{ duration: 0.85, delay: 0.6 }}
            className="mx-auto mt-20 grid max-w-4xl grid-cols-2 gap-6 md:grid-cols-4 pt-12 border-t border-slate-200/60 dark:border-slate-800/60"
          >
            {stats.map((stat, i) => (
              <div key={i} className="text-center group">
                <div className="mb-3 inline-flex h-11 w-11 items-center justify-center rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 transition-transform group-hover:scale-105">
                  {stat.icon}
                </div>
                <div className="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                  {stat.value}
                  {stat.suffix}
                </div>
                <div className="mt-1 text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">
                  {stat.label}
                </div>
              </div>
            ))}
          </motion.div>
        )}
      </motion.div>
    </section>
  );
}
