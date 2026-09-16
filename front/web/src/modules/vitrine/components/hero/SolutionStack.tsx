'use client';

/**
 * SolutionStack — visuel « Pile Leopardo » destiné au hero de la vitrine.
 *
 * Composition de trois niveaux : socle → couche horizontale → verticales.
 * Le canvas WebGL (`SolutionStack3D`) est chargé en **import dynamique** et
 * uniquement si le navigateur expose réellement un contexte WebGL ; sinon on
 * sert une composition CSS 3D équivalente (même information, zéro WebGL).
 *
 * L'intérêt du composant n'est pas décoratif : il rend lisible l'architecture
 * de l'offre — les briques horizontales sont partagées, chaque verticale s'y
 * branche. Sélectionner une verticale met en évidence les briques qu'elle
 * consomme réellement (données des manifests serveur).
 */

import { useCallback, useMemo, useState } from 'react';
import { Layers, LayoutGrid, Blocks } from 'lucide-react';
import type { AppLocale } from '@/lib/i18n';
import {
  EXTRA_MODULE_LABELS,
  HORIZONTAL_BLOCKS,
  VERTICALS,
  getSolutionStackCopy,
  type VerticalKey,
} from '@/modules/vitrine/data/solution-stack';

export interface SolutionStackProps {
  locale: AppLocale;
}

export function SolutionStack({ locale }: SolutionStackProps) {
  const copy = useMemo(() => getSolutionStackCopy(locale), [locale]);
  const extraLabels = EXTRA_MODULE_LABELS[locale] ?? EXTRA_MODULE_LABELS.fr;

  const [active, setActive] = useState<VerticalKey | null>('restaurant');
  const [hovered, setHovered] = useState<VerticalKey | null>(null);

  const focus = hovered ?? active;
  const activeVertical = active ? VERTICALS.find((v) => v.key === active) : undefined;
  const consumed = activeVertical ? new Set(activeVertical.consumes) : null;

  const labels = useMemo(
    () =>
      Object.fromEntries(
        VERTICALS.map((vertical) => [vertical.key, copy.verticals[vertical.key]]),
      ) as Record<VerticalKey, string>,
    [copy],
  );

  const toggle = useCallback((key: VerticalKey) => {
    setActive((current) => (current === key ? null : key));
  }, []);

  const layers = [
    { icon: Layers, ...copy.layerPlatform, accent: 'text-slate-400', dot: 'bg-slate-400' },
    { icon: LayoutGrid, ...copy.layerHorizontal, accent: 'text-blue-500', dot: 'bg-blue-500' },
    { icon: Blocks, ...copy.layerVertical, accent: 'text-amber-500', dot: 'bg-gradient-to-r from-amber-400 to-rose-500' },
  ];

  return (
    <div className="w-full">
      {/* Titre de section */}
      <div className="mx-auto mb-8 max-w-2xl text-center">
        <div className="mb-3 text-[11px] font-bold uppercase tracking-[0.2em] text-blue-600 dark:text-blue-400">
          {copy.eyebrow}
        </div>
        <h2 className="text-balance text-xl font-black tracking-tight text-slate-900 dark:text-white sm:text-2xl">
          {copy.title}
        </h2>
        <p className="mt-3 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
          {copy.subtitle}
        </p>
      </div>

      {/* Clean Corporate Interactive Architecture Showcase */}
      <div className="relative mx-auto max-w-5xl">
        {/* Industry Selector Tabs */}
        <div className="flex flex-wrap items-center justify-center gap-2.5 mb-8">
          {VERTICALS.map((vertical) => {
            const isActive = active === vertical.key;
            return (
              <button
                key={vertical.key}
                type="button"
                aria-pressed={isActive}
                onClick={() => toggle(vertical.key)}
                className={`inline-flex items-center gap-2.5 px-5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 shadow-sm ${
                  isActive
                    ? 'bg-blue-600 text-white shadow-blue-500/20 ring-2 ring-blue-600 ring-offset-2 dark:ring-offset-slate-900'
                    : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200/80 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-600'
                }`}
              >
                <span
                  className="w-2.5 h-2.5 rounded-full"
                  style={{ backgroundColor: isActive ? '#FFFFFF' : vertical.color }}
                />
                <span>{copy.verticals[vertical.key]}</span>
                <span className={`text-xs px-1.5 py-0.5 rounded-md ${isActive ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-500'}`}>
                  {vertical.consumes.length} modul
                </span>
              </button>
            );
          })}
        </div>

        {/* Selected Industry Detail Card (Corporate Showcase with Real Visual) */}
        {activeVertical && (
          <div className="mb-8 rounded-2xl border border-blue-100 dark:border-blue-900/40 bg-gradient-to-r from-blue-50/70 to-indigo-50/50 dark:from-blue-950/40 dark:to-indigo-950/20 p-6 sm:p-8 backdrop-blur-sm">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
              <div className="lg:col-span-7">
                <div className="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-blue-700 dark:text-blue-300 mb-2">
                  <span className="w-2 h-2 rounded-full" style={{ backgroundColor: activeVertical.color }} />
                  {copy.layerVertical.name}
                </div>
                <h3 className="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-2">
                  Solusi Terintegrasi untuk {copy.verticals[activeVertical.key]}
                </h3>
                <p className="text-xs text-slate-500 dark:text-slate-400 mb-6">
                  {activeVertical.consumes.length} modul horizontal aktif otomatis dan disesuaikan untuk kebutuhan operasional spesifik sektor ini.
                </p>

                <div className="text-xs font-bold uppercase tracking-wider text-slate-600 dark:text-slate-300 mb-3">
                  Modul Operasional Utama:
                </div>
                <div className="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                  {activeVertical.consumes.map((key) => (
                    <div
                      key={key}
                      className="flex items-center gap-2 p-2.5 rounded-xl bg-white dark:bg-slate-900 border border-blue-200/60 dark:border-blue-800/60 shadow-sm"
                    >
                      <span className="w-1.5 h-1.5 rounded-full bg-blue-600 shrink-0" />
                      <span className="text-xs font-semibold text-slate-800 dark:text-slate-200 truncate">
                        {copy.horizontals[key]}
                      </span>
                    </div>
                  ))}
                </div>

                {activeVertical.extraModules.length > 0 && (
                  <p className="mt-4 text-xs font-medium text-slate-600 dark:text-slate-400">
                    <span className="font-bold text-slate-700 dark:text-slate-300">{copy.alsoPrefix}</span>{' '}
                    {activeVertical.extraModules
                      .map((moduleKey) => extraLabels[moduleKey] ?? moduleKey)
                      .join(', ')}
                  </p>
                )}
              </div>

              {/* Right Column: Visual Product Frame */}
              <div className="lg:col-span-5">
                <div className="rounded-2xl overflow-hidden border border-slate-200/90 dark:border-slate-800 shadow-xl bg-white dark:bg-slate-900">
                  <div className="flex items-center gap-1.5 h-8 px-3.5 bg-slate-100 dark:bg-slate-800/80 border-b border-slate-200/70 dark:border-slate-700/60">
                    <span className="w-2 h-2 rounded-full bg-red-400" />
                    <span className="w-2 h-2 rounded-full bg-amber-400" />
                    <span className="w-2 h-2 rounded-full bg-emerald-400" />
                    <span className="ml-2 text-[10px] font-medium text-slate-400">Tampilan Dasbor Operasional</span>
                  </div>
                  <div className="relative aspect-[4/3] bg-slate-100 dark:bg-slate-950 overflow-hidden">
                    <Image
                      src="/screenshots/web-dashboard.png"
                      alt={`Dasbor Solusi ${copy.verticals[activeVertical.key]}`}
                      fill
                      sizes="(max-width: 1024px) 100vw, 35vw"
                      className="object-cover object-top hover:scale-105 transition-transform duration-500"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* 3 Core Architecture Layers (Corporate Clean Layout) */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          {/* Layer 1: Core Foundation */}
          <div className="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-700 dark:text-slate-300">
                <Layers className="w-5 h-5" />
              </div>
              <div>
                <h4 className="font-bold text-slate-900 dark:text-white text-base">
                  {copy.layerPlatform.name}
                </h4>
                <span className="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                  Fondasi Sistem
                </span>
              </div>
            </div>
            <p className="text-xs leading-relaxed text-slate-600 dark:text-slate-400">
              {copy.layerPlatform.desc}
            </p>
          </div>

          {/* Layer 2: 16 Shared Modules */}
          <div className="rounded-2xl border border-blue-200/80 dark:border-blue-900/60 bg-blue-50/40 dark:bg-blue-950/20 p-6 shadow-sm">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-400">
                <LayoutGrid className="w-5 h-5" />
              </div>
              <div>
                <h4 className="font-bold text-slate-900 dark:text-white text-base">
                  {copy.layerHorizontal.name}
                </h4>
                <span className="text-[11px] font-semibold text-blue-600 dark:text-blue-400 uppercase tracking-wider">
                  16 Modul Terintegrasi
                </span>
              </div>
            </div>
            <p className="text-xs leading-relaxed text-slate-600 dark:text-slate-400">
              {copy.layerHorizontal.desc}
            </p>
          </div>

          {/* Layer 3: 4 Business Verticals */}
          <div className="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <Blocks className="w-5 h-5" />
              </div>
              <div>
                <h4 className="font-bold text-slate-900 dark:text-white text-base">
                  {copy.layerVertical.name}
                </h4>
                <span className="text-[11px] font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider">
                  4 Spesialisasi Bisnis
                </span>
              </div>
            </div>
            <p className="text-xs leading-relaxed text-slate-600 dark:text-slate-400">
              {copy.layerVertical.desc}
            </p>
          </div>
        </div>

        {/* All 16 Modules Grid - Clean Corporate Presentation */}
        <div className="rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4 pb-4 border-b border-slate-100 dark:border-slate-800">
            <span className="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
              Katalog 16 Modul Standar RMIH
            </span>
            <span className="text-xs text-slate-500">
              {active ? `Menyorot modul untuk ${copy.verticals[active]}` : copy.hint}
            </span>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            {HORIZONTAL_BLOCKS.map((block) => {
              const isConsumed = consumed === null || consumed.has(block.key);
              return (
                <div
                  key={block.key}
                  className={`flex items-center gap-2 p-3 rounded-xl border text-xs font-medium transition-all duration-200 ${
                    isConsumed
                      ? 'border-blue-200 bg-blue-50/80 text-blue-900 dark:border-blue-800 dark:bg-blue-950/40 dark:text-blue-200 shadow-sm font-semibold'
                      : 'border-slate-200/70 bg-slate-50/50 text-slate-400 dark:border-slate-800 dark:bg-slate-900/50 dark:text-slate-500 opacity-60'
                  }`}
                >
                  <span
                    className={`w-1.5 h-1.5 rounded-full shrink-0 ${
                      isConsumed ? 'bg-blue-600' : 'bg-slate-300 dark:bg-slate-700'
                    }`}
                  />
                  <span className="truncate">{copy.horizontals[block.key]}</span>
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </div>
  );
}

export default SolutionStack;
