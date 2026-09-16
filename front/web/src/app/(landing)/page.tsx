'use client';

import { Sparkles, Play, Zap, Users, TrendingUp, Star } from 'lucide-react';
import { useDarkMode } from '@/modules/vitrine/hooks/useDarkMode';
import { useState } from 'react';
import {
  Navbar,
  useScrollReveal,
  FAQSection,
} from '@/modules/vitrine';
import { RmihHero } from '@/modules/vitrine/components/RmihHero';
import { RmihWhyChoose } from '@/modules/vitrine/components/RmihWhyChoose';
import { RmihFeaturedModules } from '@/modules/vitrine/components/RmihFeaturedModules';
import { RmihStatsBanner } from '@/modules/vitrine/components/RmihStatsBanner';
import { RmihFeatureDetails } from '@/modules/vitrine/components/RmihFeatureDetails';
import { RmihGuaranteeBanner } from '@/modules/vitrine/components/RmihGuaranteeBanner';
import { RmihImplementationFlow } from '@/modules/vitrine/components/RmihImplementationFlow';
import { RmihCalloutBanner } from '@/modules/vitrine/components/RmihCalloutBanner';
import { RmihFAQ } from '@/modules/vitrine/components/RmihFAQ';
import { RmihFooter } from '@/modules/vitrine/components/RmihFooter';
import { useVitrineLocale } from '@/modules/vitrine/lib/vitrine-locale';
import { StickyMobileCTA } from '@/components/StickyMobileCTA';

export default function LandingPage() {
  const { isDark, toggleDarkMode } = useDarkMode();
  useScrollReveal();
  const { locale, direction } = useVitrineLocale();

  return (
    <div
      dir={direction}
      className={`min-h-screen transition-colors duration-500 ${
        isDark ? 'dark bg-slate-950' : 'bg-white'
      }`}
    >
      <Navbar isDark={isDark} onToggleDark={toggleDarkMode} />

      <main>
        {/* 1. Hero Section (2 Kolom: Kiri Copywriting & Pill Checklist, Kanan Real Mockup Web & Mobile) */}
        <RmihHero
          badge="Platform Management HR & Operasional Terlengkap"
          headlinePrefix="Ekosistem"
          headlineHighlight="software"
          headlineSuffix="terpadu untuk mendukung pertumbuhan bisnis profesional"
          subheadline="RMIH (Resources Management Integrated Human) adalah solusi lengkap untuk mengelola karyawan, absensi, operasional, dan HR bisnis Anda dalam satu platform yang mudah digunakan."
          benefits={['Lebih Efisien', 'Lebih Terorganisir', 'Siap untuk Masa Depan']}
          primaryCtaText="Mulai Sekarang"
          primaryCtaHref="/signup"
          secondaryCtaText="Lihat Demo"
          secondaryCtaHref="#detail-fitur"
          floatingBadgeText="Kelola bisnis Anda, lebih mudah!"
        />

        {/* 2. Mengapa Memilih RMIH? (4 Kolom Card Ikon Biru) */}
        <RmihWhyChoose />

        {/* 3. Fitur Unggulan (6 Card Putih Minimalis dengan Chevron) */}
        <RmihFeaturedModules />

        {/* 4. Stat Counter Ribbon (Banner Biru 4 Angka Kunci) */}
        <RmihStatsBanner />

        {/* 5. Detail Fitur RMIH (6 Card Berwarna dengan Checklist Rinci) */}
        <RmihFeatureDetails />

        {/* 6. Paket Lengkap Garansi 1 Tahun & Support (Banner Gelap Corporate) */}
        <RmihGuaranteeBanner />

        {/* 7. Alur Implementasi (5 Langkah Berurutan 1 sampai 5) */}
        <RmihImplementationFlow />

        {/* 8. Banner Siap Meningkatkan Efisiensi Bisnis Anda (Callout Box dengan 4 Checklist Kanan) */}
        <RmihCalloutBanner />

        {/* 9. FAQ Section (Pertanyaan yang Sering Diajukan - Simple & Rapi persis gambar 1) */}
        <RmihFAQ />
      </main>

      {/* Modern Minimalist RMIH Footer */}
      <RmihFooter />

      {/* Sticky Mobile CTA */}
      <StickyMobileCTA locale={locale} />
    </div>
  );
}
