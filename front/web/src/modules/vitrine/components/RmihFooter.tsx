'use client';

import Link from 'next/link';
import { RmihLogo } from './RmihLogo';
import { MessageCircle, Send, Globe } from 'lucide-react';

export function RmihFooter() {
  return (
    <footer className="bg-slate-950 text-white border-t border-slate-850 py-12">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div className="flex flex-col md:flex-row items-center justify-between gap-8 pb-10 border-b border-slate-900">
          {/* Logo & Tagline */}
          <div className="flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
            <RmihLogo size="md" theme="dark" href="/" />
            <div className="h-4 w-px bg-slate-800 hidden sm:block" />
            <p className="text-xs text-slate-400">
              Solusi Lengkap Manajemen HR & Operasional Bisnis
            </p>
          </div>

          {/* Quick Nav Links */}
          <nav className="flex flex-wrap items-center justify-center gap-6 text-sm font-medium text-slate-400">
            <Link href="/" className="hover:text-white transition-colors">
              Beranda
            </Link>
            <Link href="/#detail-fitur" className="hover:text-white transition-colors">
              Fitur
            </Link>
            <Link href="/#detail-fitur" className="hover:text-white transition-colors">
              Solusi
            </Link>
            <Link href="/pricing" className="hover:text-white transition-colors">
              Harga
            </Link>
            <Link href="/about" className="hover:text-white transition-colors">
              Tentang Kami
            </Link>
            <Link href="/contact" className="hover:text-white transition-colors">
              Kontak
            </Link>
          </nav>

          {/* Social & Contact Icons */}
          <div className="flex items-center gap-3">
            <Link
              href="https://wa.me/6282187827382"
              target="_blank"
              rel="noopener noreferrer"
              className="w-9 h-9 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-emerald-600 hover:border-emerald-600 transition-all"
              aria-label="WhatsApp (+62 821-8782-7382)"
            >
              <MessageCircle className="w-4 h-4" />
            </Link>
            <Link
              href="https://github.com/esnpendosa/hr-rozitech"
              target="_blank"
              rel="noopener noreferrer"
              className="w-9 h-9 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-blue-600 hover:border-blue-600 transition-all text-xs font-bold"
              aria-label="GitHub Repository"
            >
              Gh
            </Link>
            <Link
              href="https://rozitech.co.id"
              target="_blank"
              rel="noopener noreferrer"
              className="w-9 h-9 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-400 hover:text-white hover:bg-blue-600 hover:border-blue-600 transition-all"
              aria-label="Website Rozitech"
            >
              <Globe className="w-4 h-4" />
            </Link>
          </div>
        </div>

        {/* Bottom Copyright */}
        <div className="pt-8 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
          <p>© {new Date().getFullYear()} RMIH (Resources Management Integrated Human). All rights reserved.</p>
          <div className="flex items-center gap-5">
            <Link href="/privacy" className="hover:text-slate-400 transition-colors">
              Kebijakan Privasi
            </Link>
            <Link href="/terms" className="hover:text-slate-400 transition-colors">
              Syarat & Ketentuan
            </Link>
          </div>
        </div>

      </div>
    </footer>
  );
}
