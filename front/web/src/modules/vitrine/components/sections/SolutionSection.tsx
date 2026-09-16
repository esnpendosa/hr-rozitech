import Image from 'next/image';
import { motion } from 'framer-motion';
import { CheckCircle2, ArrowRight } from 'lucide-react';
import Link from 'next/link';

export interface SolutionFeature {
  title: string;
  description: string;
}

export interface SolutionSectionProps {
  title: string;
  subtitle: string;
  description: string;
  features: SolutionFeature[];
  badge?: {
    text: string;
    icon?: React.ReactNode;
  };
}

export function SolutionSection({
  title,
  subtitle,
  description,
  features,
  badge,
}: SolutionSectionProps) {
  return (
    <section className="relative py-20 overflow-hidden bg-slate-50/50 dark:bg-slate-900/30 border-t border-slate-200/60 dark:border-slate-800/60">
      <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
          {/* Left Column: Visual Mockup / Product Screenshot */}
          <motion.div
            initial={{ opacity: 0, x: -30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="lg:col-span-6 relative"
          >
            <div className="relative mx-auto rounded-3xl overflow-hidden border border-slate-200/90 dark:border-slate-800 shadow-2xl bg-white dark:bg-slate-900 group">
              <div className="flex items-center gap-1.5 h-10 px-4 bg-slate-100 dark:bg-slate-800/80 border-b border-slate-200/70 dark:border-slate-700/60">
                <span className="w-2.5 h-2.5 rounded-full bg-red-400" />
                <span className="w-2.5 h-2.5 rounded-full bg-amber-400" />
                <span className="w-2.5 h-2.5 rounded-full bg-emerald-400" />
                <span className="ml-3 text-[11px] font-medium text-slate-400">app.rmih.id/dashboard</span>
              </div>
              <div className="relative aspect-[16/10] bg-slate-100 dark:bg-slate-950 overflow-hidden">
                <Image
                  src="/screenshots/web-dashboard.png"
                  alt="RMIH Platform Solution Overview"
                  fill
                  sizes="(max-width: 1024px) 100vw, 50vw"
                  className="object-cover object-top transition-transform duration-500 group-hover:scale-105"
                />
              </div>
            </div>

            {/* Subtle Floating Badge */}
            <div className="absolute -bottom-5 -right-3 sm:right-6 bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700 shadow-xl rounded-2xl p-4 flex items-center gap-3.5 backdrop-blur-md">
              <div className="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-blue-500/30">
                ✓
              </div>
              <div>
                <p className="text-xs font-bold text-slate-900 dark:text-white">Ekosistem Terintegrasi</p>
                <p className="text-[11px] text-slate-500 dark:text-slate-400">Web, Mobile & Biometrik</p>
              </div>
            </div>
          </motion.div>

          {/* Right Column: Corporate Value Proposition */}
          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.1 }}
            className="lg:col-span-6"
          >
            {badge && (
              <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-800/60 text-blue-700 dark:text-blue-300 text-xs font-bold uppercase tracking-wider mb-4">
                {badge.icon && <span className="w-1.5 h-1.5 rounded-full bg-blue-600" />}
                {badge.text}
              </div>
            )}

            <h2 className="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight mb-4">
              {title}{' '}
              {subtitle && (
                <span className="text-blue-600 dark:text-blue-400">
                  {subtitle}
                </span>
              )}
            </h2>

            <p className="text-base text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
              {description}
            </p>

            {/* Feature Points Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
              {features.map((feature, index) => (
                <div
                  key={index}
                  className="p-4 rounded-xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:border-blue-300 dark:hover:border-blue-600 transition-colors"
                >
                  <div className="flex items-start gap-3">
                    <CheckCircle2 className="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
                    <div>
                      <h3 className="text-sm font-bold text-slate-900 dark:text-white mb-1">
                        {feature.title}
                      </h3>
                      <p className="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                        {feature.description}
                      </p>
                    </div>
                  </div>
                </div>
              ))}
            </div>

            <div className="flex items-center gap-4">
              <Link
                href="/signup"
                className="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold shadow-lg shadow-blue-600/20 transition-all hover:scale-105"
              >
                Coba Solusi RMIH
                <ArrowRight className="w-4 h-4" />
              </Link>
              <Link
                href="/demo"
                className="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm font-semibold transition-colors"
              >
                Jadwalkan Konsultasi
              </Link>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
}
