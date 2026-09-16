import Link from 'next/link';

/**
 * 404 global de l'application.
 *
 * Avant ce fichier, tout `notFound()` (ou toute URL inconnue) tombait sur la
 * page 404 par défaut de Next.js : aucune identité visuelle, aucun chemin de
 * retour vers le produit. Ce composant rend un 404 cohérent avec la marque et
 * propose des sorties utiles.
 */
export default function NotFound() {
  return (
    <main className="flex min-h-screen items-center justify-center bg-slate-50 px-6 py-16 dark:bg-slate-950">
      <div className="w-full max-w-lg text-center">
        <p className="text-sm font-semibold uppercase tracking-[0.2em] text-brand-600 dark:text-brand-400">
          Kesalahan 404
        </p>
        <h1 className="mt-4 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl dark:text-white">
          Halaman Tidak Ditemukan
        </h1>
        <p className="mt-4 text-base leading-relaxed text-slate-600 dark:text-slate-400">
          Halaman ini tidak tersedia atau telah dipindahkan. Silakan periksa kembali alamat URL, atau kembali ke halaman beranda.
        </p>
        <div className="mt-8 flex flex-wrap items-center justify-center gap-3">
          <Link
            href="/"
            className="inline-flex items-center rounded-xl bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
          >
            Kembali ke Beranda
          </Link>
          <Link
            href="/contact"
            className="inline-flex items-center rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-900"
          >
            Hubungi Dukungan
          </Link>
        </div>
      </div>
    </main>
  );
}
