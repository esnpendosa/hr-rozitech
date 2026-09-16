'use client'

import { useState } from 'react'
import Link from 'next/link'
import { motion } from 'framer-motion'
import { ArrowRight, Check, Star, Users } from 'lucide-react'
import { getPricingPlans, showsCurrency } from '../data/pricing'
import { useVitrineLocale } from '../lib/vitrine-locale'
import { t } from '@/lib/i18n/locale-catalog'

export function planNameToCheckoutKey(planName?: string): 'free' | 'pilot' | 'operations' | 'enterprise' {
  const name = (planName ?? '').trim().toLowerCase()
  if (name.includes('pilot') || name.includes('starter')) return 'pilot'
  if (name.includes('operations') || name.includes('business')) return 'operations'
  if (name.includes('scale') || name.includes('enterprise')) return 'enterprise'
  if (name.includes('free')) return 'free'
  return 'free'
}

function getPlanCtaHref(price: string, planName?: string, isAnnual?: boolean) {
  if (!showsCurrency(price)) return '/contact?topic=enterprise'
  const planKey = planNameToCheckoutKey(planName)
  if (planKey === 'free') return '/signup?source=home_free'
  if (planKey === 'pilot') return '/signup?source=home_pilot'
  return `/signup?source=home_${planKey}`
}

export function PricingSection() {
  const { copy, locale } = useVitrineLocale()
  const pricingPlans = getPricingPlans(locale)
  const [isAnnual, setIsAnnual] = useState(true)
  const toggle = { monthly: t(locale, 'pricing.section.toggleMonthly'), annual: t(locale, 'pricing.section.toggleAnnual') }

  return (
    <section id="tarifs" className="relative py-32 overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-b from-white via-slate-50/60 to-white dark:from-slate-950 dark:via-slate-900/60 dark:to-slate-950" />

      <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-12 gsap-reveal">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-600/[0.08] border border-blue-500/15 text-blue-700 dark:text-blue-400 text-sm font-semibold mb-6">
            <span className="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse" />
            {copy.pricing.badge}
          </div>
          <h2 className="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 dark:text-white mb-6 tracking-tight">
            {copy.pricing.title}{' '}
            <span className="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
              {copy.pricing.titleHighlight}
            </span>
          </h2>
          <p className="text-xl text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
            {copy.pricing.subtitle}
          </p>
        </div>

        <div className="flex items-center justify-center gap-3 mb-16">
          <span className={`text-sm font-medium transition-colors ${!isAnnual ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-400'}`}>
            {toggle.monthly}
          </span>
          <button
            onClick={() => setIsAnnual(!isAnnual)}
            className="relative w-14 h-7 rounded-full bg-blue-600 transition-colors shadow-inner shadow-blue-700/30"
            aria-label={t(locale, 'pricing.section.toggleAria')}
          >
            <motion.div
              className="absolute top-0.5 w-6 h-6 rounded-full bg-white shadow-md"
              animate={{ left: isAnnual ? '1.75rem' : '0.125rem' }}
              transition={{ type: 'spring', stiffness: 500, damping: 30 }}
            />
          </button>
          <span className={`text-sm font-medium transition-colors ${isAnnual ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-400'}`}>
            {toggle.annual}
          </span>
          {isAnnual && (
            <motion.span
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              className="ml-1 px-3 py-1 text-xs font-bold text-blue-700 bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 rounded-full"
            >
              {copy.pricing.annualSavings}
            </motion.span>
          )}
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 max-w-7xl mx-auto">
          {pricingPlans.map((plan, index) => {
            const displayPrice = isAnnual ? plan.annualPrice : plan.price
            const displayPeriod = isAnnual ? plan.annualPeriod : plan.period
            const hasNumericPrice = showsCurrency(displayPrice)

            return (
              <motion.div
                key={`${plan.name}-${index}`}
                initial={{ opacity: 0, y: 35 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ duration: 0.55, delay: index * 0.08 }}
                whileHover={{ y: -8, transition: { duration: 0.22 } }}
                className={`relative flex flex-col rounded-3xl transition-all duration-300 ${
                  plan.popular
                    ? 'bg-gradient-to-b from-blue-500 via-indigo-600 to-blue-700 p-[2px] shadow-2xl shadow-blue-600/25 ring-1 ring-blue-500/40'
                    : 'bg-gradient-to-b from-slate-200/90 to-slate-200/50 dark:from-slate-800 dark:to-slate-800/40 p-px hover:shadow-xl hover:shadow-blue-500/10'
                }`}
              >
                <div className="relative flex-1 rounded-[22px] bg-white dark:bg-slate-950 p-7 flex flex-col justify-between">
                  {plan.popular && (
                    <div className="absolute -top-3.5 left-1/2 -translate-x-1/2 z-10">
                      <span className="flex items-center gap-1.5 px-4 py-1.5 bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 text-white text-[11px] font-black uppercase tracking-wider rounded-full shadow-lg shadow-blue-500/30">
                        <Star className="w-3.5 h-3.5 fill-white" />
                        {copy.pricing.recommended}
                      </span>
                    </div>
                  )}

                  <div>
                    <div className="text-center mb-6">
                      <h3 className="text-xl font-black text-slate-900 dark:text-white mb-1 tracking-tight">{plan.name}</h3>
                      <p className="text-xs text-slate-500 dark:text-slate-400 mb-3 min-h-[32px]">{plan.description}</p>
                      <div className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-900 text-xs font-medium text-slate-700 dark:text-slate-300 mb-5 border border-slate-200/40 dark:border-slate-800">
                        <Users className="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
                        {plan.employeeLimit}
                      </div>
                      <div className="flex items-baseline justify-center gap-1">
                        {plan.price === '0' ? (
                          <span className="text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                            {t(locale, 'pricing.section.freeLabel')}
                          </span>
                        ) : (
                          <>
                            {hasNumericPrice && (
                              <span className="text-base font-black text-blue-600 dark:text-blue-400 mr-1">
                                {copy.pricing.currency}
                              </span>
                            )}
                            <span className="text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                              {displayPrice}
                            </span>
                          </>
                        )}
                      </div>
                      {displayPeriod && (
                        <span className="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-1 block">{displayPeriod}</span>
                      )}
                      {plan.priceNote && (
                        <p className="mt-1 text-[11px] text-slate-500 dark:text-slate-400">{plan.priceNote}</p>
                      )}
                      {isAnnual && hasNumericPrice && (
                        <div className="mt-1">
                          <span className="text-xs text-slate-400 line-through dark:text-slate-500">{copy.pricing.currency} {plan.price}</span>
                        </div>
                      )}
                    </div>

                    <ul className="space-y-3 mb-8">
                      {plan.features.map((feature, featureIndex) => (
                        <li key={`${plan.name}-feature-${featureIndex}`} className="flex items-start gap-2.5">
                          <div className={`flex-shrink-0 w-4 h-4 rounded-full flex items-center justify-center mt-0.5 ${
                            plan.popular
                              ? 'bg-blue-600 text-white'
                              : 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400'
                          }`}>
                            <Check className="w-3 h-3 stroke-[2.5]" />
                          </div>
                          <span className="text-xs font-medium text-slate-700 dark:text-slate-300 leading-snug">{feature}</span>
                        </li>
                      ))}
                    </ul>
                  </div>

                  <Link
                    href={getPlanCtaHref(displayPrice, plan.name, isAnnual)}
                    className={`flex items-center justify-center gap-2 w-full py-3 rounded-xl font-bold text-sm transition-all duration-200 ${
                      plan.popular
                        ? 'bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-[1.02] active:scale-[0.98]'
                        : plan.price === '0'
                          ? 'bg-slate-900 dark:bg-slate-800 text-white hover:bg-black dark:hover:bg-slate-700 hover:scale-[1.01] active:scale-[0.98]'
                          : hasNumericPrice
                            ? 'bg-blue-600 hover:bg-blue-700 text-white hover:scale-[1.01] active:scale-[0.98] shadow-md shadow-blue-500/10'
                            : 'border-2 border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800 hover:scale-[1.01] active:scale-[0.98]'
                    }`}
                  >
                    {plan.cta}
                    <ArrowRight className="w-4 h-4" />
                  </Link>
                </div>
              </motion.div>
            )
          })}
        </div>

        <div className="mt-12 text-center">
          <Link
            href="/pricing"
            className="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors"
          >
            {t(locale, 'pricing.section.fullComparison')}
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      </div>
    </section>
  )
}
