import type { AppLocale } from '@/lib/i18n'

// Contenu des pages /guides/* par locale (issue #3248 — résiduel : les 3
// guides étaient 100 % FR codé en dur). Pattern #2605/#3764 : les chaînes
// vivent ici, les icônes/mise en page restent dans les pages.

export type GuideStat = {
  title: string
  description: string
}

export type GuideSection = {
  title: string
  description: string
}

export type GuideContent = {
  hero: {
    headline: string
    subheadline: string
    badge: string
    ctaPrimary: string
    ctaSecondary: string
  }
  stats: GuideStat[]
  sectionTitle: string
  sections: GuideSection[]
  cta: {
    headline: string
    subheadline: string
    ctaPrimary: string
    ctaSecondary: string
  }
}

export type GuidesContent = {
  rhStartup: GuideContent
  planningEmployes: GuideContent
  checklistPaie: GuideContent
}

export const guidesPageCopy: Record<AppLocale, GuidesContent> = {
  id: {
    rhStartup: {
      hero: {
        headline: 'Panduan Lengkap Manajemen HR & Absensi Bisnis',
        subheadline:
          'Semua yang perlu Anda ketahui untuk mengelola karyawan, kehadiran biometrik, dan penggajian di UMKM & Startup',
        badge: 'Panduan Praktis Gratis',
        ctaPrimary: 'Unduh Panduan (PDF)',
        ctaSecondary: 'Konsultasi Sekarang',
      },
      stats: [
        { title: '10 Bab Lengkap', description: 'Mencakup seluruh aspek pengelolaan SDM dan operasional tim' },
        { title: 'Format Siap Pakai', description: 'Dilengkapi template SOP absensi, jadwal shift, dan form lembur' },
        { title: '100% Gratis', description: 'Dapat diunduh dan dipelajari langsung tanpa biaya' },
      ],
      sectionTitle: 'Daftar Isi & Pembahasan',
      sections: [
        { title: '1. Fondasi Manajemen SDM untuk Bisnis Bertumbuh', description: 'Pilar utama kepatuhan ketenagakerjaan dan transparansi jam kerja' },
        { title: '2. Rekrutmen & Onboarding Karyawan Baru', description: 'Cara menjaring talenta terbaik dan orientasi kerja yang efektif' },
        { title: '3. Kontrak Kerja & Peraturan Perusahaan (PP/PKB)', description: 'Memahami PKWT, PKWTT, dan perlindungan legalitas usaha Anda' },
        { title: '4. Pengelolaan Absensi & Shift Kerja', description: 'Integrasi mesin biometrik ZKTeco dan absensi mobile geofencing' },
        { title: '5. Kebijakan Cuti, Izin & Lembur Transparan', description: 'Alur persetujuan digital berjenjang tanpa birokrasi kertas' },
        { title: '6. Otomasi Payroll & Perhitungan Gaji Bersih', description: 'Sinkronisasi kehadiran langsung ke komponen tunjangan dan potongan' },
        { title: '7. Kepatuhan Pajak PPh 21 & Iuran BPJS', description: 'Ketentuan perhitungan PPh 21 TER dan BPJS Ketenagakerjaan/Kesehatan' },
        { title: '8. Evaluasi Kinerja (KPI & OKR)', description: 'Metode penilaian objektif untuk memotivasi produktivitas tim' },
        { title: '9. Keamanan Data & Privasi Informasi Karyawan', description: 'Perlindungan berkas digital dan riwayat gaji sesuai UU PDP' },
        { title: '10. Skalabilitas Multi-Cabang & Outlet', description: 'Mempersiapkan sistem HR untuk ekspansi bisnis tanpa hambatan teknis' },
      ],
      cta: {
        headline: 'Siap Mentransformasi Pengelolaan HR Anda?',
        subheadline: 'Terapkan sistem HR modern terintegrasi bersama RMIH dan CV. Rozitech Multimedia Indonesia',
        ctaPrimary: 'Unduh Panduan Sekarang',
        ctaSecondary: 'Coba RMIH Gratis',
      },
    },
    planningEmployes: {
      hero: {
        headline: 'Template Jadwal & Shift Kerja Karyawan',
        subheadline: 'Template Excel praktis untuk mengatur shift harian dan jam kerja tim Anda',
        badge: 'Template Gratis',
        ctaPrimary: 'Unduh Template (Excel)',
        ctaSecondary: 'Coba RMIH Sekarang',
      },
      stats: [
        { title: 'Mudah Disesuaikan', description: 'Dapat langsung diedit sesuai format shift dan jam operasional outlet' },
        { title: 'Formula Otomatis', description: 'Menghitung total jam kerja dan hari hadir secara instan' },
        { title: '100% Gratis', description: 'Unduh langsung format spreadsheet tanpa syarat' },
      ],
      sectionTitle: 'Struktur Lembar Kerja Template',
      sections: [
        { title: 'Daftar Karyawan & Divisi', description: 'Data master karyawan, jabatan, dan penugasan lokasi kerja' },
        { title: 'Jadwal Shift Bulanan', description: 'Kalender kerja dengan kode shift pagi, siang, malam, dan libur' },
        { title: 'Rekap Jam Kerja & Lembur', description: 'Penghitungan akumulasi jam kerja mingguan dan bulanan' },
        { title: 'Laporan Ketidakhadiran', description: 'Pencatatan izin sakit, cuti tahunan, dan dispensasi' },
      ],
      cta: {
        headline: 'Atur Jadwal Tim Lebih Praktis & Rapi',
        subheadline: 'Gunakan template ini atau otomatiskan jadwal shift dengan modul RMIH',
        ctaPrimary: 'Unduh Template Excel',
        ctaSecondary: 'Pelajari Fitur Shift RMIH',
      },
    },
    checklistPaie: {
      hero: {
        headline: 'Checklist Kesiapan Payroll & Penggajian',
        subheadline: 'Pastikan proses penggajian bulanan akurat, tepat waktu, dan patuh regulasi',
        badge: 'Checklist Gratis',
        ctaPrimary: 'Unduh Checklist (PDF)',
        ctaSecondary: 'Coba RMIH Payroll',
      },
      stats: [
        { title: '50+ Poin Verifikasi', description: 'Pengecekan menyeluruh dari rekonsiliasi absensi hingga slip gaji' },
        { title: 'Sesuai UU Ketenagakerjaan', description: 'Kompilasi komponen wajib, PPh 21, dan jaminan sosial BPJS' },
        { title: '100% Gratis', description: 'Unduh langsung berkas panduan dalam format PDF' },
      ],
      sectionTitle: 'Tahapan Checklist Penggajian',
      sections: [
        { title: '1. Pra-Penggajian (Cut-Off Periode)', description: 'Tutup buku absensi, rekap klaim lembur, dan verifikasi izin cuti' },
        { title: '2. Pemrosesan Komponen Gaji', description: 'Hitung gaji pokok, tunjangan tetap/variabel, dan insentif kehadiran' },
        { title: '3. Pemotongan Wajib & Sukarela', description: 'Kalkulasi PPh 21 TER, BPJS TK/Kes, serta pinjaman/kasbon' },
        { title: '4. Verifikasi & Approval Finansial', description: 'Pemeriksaan silang total transfer bank dan persetujuan direksi' },
        { title: '5. Distribusi Slip Gaji & Pelaporan', description: 'Pengiriman slip gaji digital terenkripsi dan pelaporan SPT Masa' },
      ],
      cta: {
        headline: 'Hindari Kesalahan Hitung Gaji Karyawan',
        subheadline: 'Gunakan checklist ini atau beralih ke otomasi payroll RMIH dalam hitungan menit',
        ctaPrimary: 'Unduh Checklist PDF',
        ctaSecondary: 'Demo Payroll RMIH',
      },
    },
  },
  fr: {
    rhStartup: {
      hero: {
        headline: 'Guide Complet RH pour Startup',
        subheadline:
          'Tout ce que vous devez savoir pour gérer vos employés en startup',
        badge: 'Guide Gratuit',
        ctaPrimary: 'Télécharger le Guide (PDF)',
        ctaSecondary: 'Essai Gratuit',
      },
      stats: [
        { title: '10 Chapitres', description: 'Couvrant tous les aspects de la gestion RH en startup' },
        { title: '50+ Pages', description: 'Contenu détaillé avec exemples et templates' },
        { title: '100% Gratuit', description: 'Aucune inscription requise, téléchargez directement' },
      ],
      sectionTitle: 'Contenu du Guide',
      sections: [
        { title: 'Fondamentaux de la RH en Startup', description: 'Pourquoi la RH est importante et les 3 piliers essentiels' },
        { title: 'Recrutement et Onboarding', description: 'Comment trouver et intégrer les bons talents' },
        { title: 'Gestion des Contrats et Conformité', description: 'Respecter la loi et protéger votre entreprise' },
        { title: 'Gestion de la Paie', description: 'Automatiser et sécuriser votre paie' },
        { title: 'Gestion des Absences et Congés', description: 'Gérer efficacement les congés et absences' },
        { title: 'Culture et Engagement', description: 'Créer une culture forte et engager vos employés' },
        { title: 'Outils et Systèmes', description: 'Choisir les bons outils pour votre startup' },
        { title: 'Gestion des Performances', description: 'Évaluer et développer vos employés' },
        { title: 'Santé et Sécurité', description: 'Responsabilités légales et bien-être' },
        { title: 'Croissance et Scalabilité', description: 'Préparer votre RH pour la croissance' },
      ],
      cta: {
        headline: 'Prêt à transformer votre RH?',
        subheadline: "Téléchargez le guide et commencez dès aujourd'hui",
        ctaPrimary: 'Télécharger Maintenant',
        ctaSecondary: 'Essayer Leopardo',
      },
    },
    planningEmployes: {
      hero: {
        headline: 'Modèle Planning Employés',
        subheadline: 'Template Excel gratuit pour gérer le planning de votre équipe',
        badge: 'Template Gratuit',
        ctaPrimary: 'Télécharger le Template (Excel)',
        ctaSecondary: 'Essai Gratuit',
      },
      stats: [
        { title: 'Flexible', description: 'Adaptez le template à vos besoins' },
        { title: 'Facile à Utiliser', description: 'Pas de configuration complexe' },
        { title: '100% Gratuit', description: 'Téléchargez directement en Excel' },
      ],
      sectionTitle: 'Contenu du Template',
      sections: [
        { title: 'Feuille Employés', description: 'Liste de vos employés avec informations de base' },
        { title: 'Planning Mensuel', description: 'Vue mensuelle du planning avec jours de travail' },
        { title: 'Heures de Travail', description: 'Suivi des heures et des pauses' },
        { title: 'Rapports', description: 'Rapports automatiques sur le planning' },
      ],
      cta: {
        headline: 'Gérez votre planning facilement',
        subheadline: "Téléchargez le template et commencez dès aujourd'hui",
        ctaPrimary: 'Télécharger Maintenant',
        ctaSecondary: 'Essayer Leopardo',
      },
    },
    checklistPaie: {
      hero: {
        headline: 'Checklist Paie',
        subheadline:
          'Assurez la conformité de votre paie avec cette checklist complète',
        badge: 'Guide Gratuit',
        ctaPrimary: 'Télécharger la Checklist (PDF)',
        ctaSecondary: 'Essai Gratuit',
      },
      stats: [
        { title: '50+ Points', description: 'Vérifications complètes pour votre paie' },
        { title: 'Conformité assistée', description: 'Vérifiez vos échéances paie et sociales' },
        { title: '100% Gratuit', description: 'Téléchargez directement en PDF' },
      ],
      sectionTitle: 'Sections de la Checklist',
      sections: [
        { title: 'Avant la Paie', description: 'Préparation et vérifications préalables' },
        { title: 'Pendant la Paie', description: 'Calculs et vérifications en cours' },
        { title: 'Après la Paie', description: 'Validation et archivage' },
        { title: 'Conformité', description: 'Mises à jour et changements en vigueur' },
        { title: 'Sécurité', description: 'Protection des données et conformité' },
      ],
      cta: {
        headline: 'Assurez votre conformité paie',
        subheadline: 'Téléchargez la checklist et vérifiez chaque point',
        ctaPrimary: 'Télécharger Maintenant',
        ctaSecondary: 'Essayer Leopardo',
      },
    },
  },
  en: {
    rhStartup: {
      hero: {
        headline: 'Complete HR Guide for Startups',
        subheadline: 'Everything you need to know to manage your employees in a startup',
        badge: 'Free Guide',
        ctaPrimary: 'Download the Guide (PDF)',
        ctaSecondary: 'Start free trial',
      },
      stats: [
        { title: '10 Chapters', description: 'Covering every aspect of HR management in a startup' },
        { title: '50+ Pages', description: 'In-depth content with examples and templates' },
        { title: '100% Free', description: 'No sign-up required, download directly' },
      ],
      sectionTitle: 'Guide Contents',
      sections: [
        { title: 'HR Fundamentals for Startups', description: 'Why HR matters and the 3 essential pillars' },
        { title: 'Recruitment and Onboarding', description: 'How to find and integrate the right talent' },
        { title: 'Contracts and Compliance', description: 'Stay legal and protect your business' },
        { title: 'Payroll Management', description: 'Automate and secure your payroll' },
        { title: 'Absences and Leave Management', description: 'Manage leave and absences effectively' },
        { title: 'Culture and Engagement', description: 'Build a strong culture and engage employees' },
        { title: 'Tools and Systems', description: 'Choose the right tools for your startup' },
        { title: 'Performance Management', description: 'Evaluate and develop your employees' },
        { title: 'Health and Safety', description: 'Legal responsibilities and wellbeing' },
        { title: 'Growth and Scalability', description: 'Prepare your HR for growth' },
      ],
      cta: {
        headline: 'Ready to transform your HR?',
        subheadline: 'Download the guide and get started today',
        ctaPrimary: 'Download Now',
        ctaSecondary: 'Try Leopardo',
      },
    },
    planningEmployes: {
      hero: {
        headline: 'Employee Schedule Template',
        subheadline: 'Free Excel template to manage your team’s schedule',
        badge: 'Free Template',
        ctaPrimary: 'Download the Template (Excel)',
        ctaSecondary: 'Start free trial',
      },
      stats: [
        { title: 'Flexible', description: 'Adapt the template to your needs' },
        { title: 'Easy to Use', description: 'No complex setup required' },
        { title: '100% Free', description: 'Download directly in Excel' },
      ],
      sectionTitle: 'Template Contents',
      sections: [
        { title: 'Employees Sheet', description: 'List of your employees with basic information' },
        { title: 'Monthly Schedule', description: 'Monthly view of the schedule with working days' },
        { title: 'Working Hours', description: 'Track hours and breaks' },
        { title: 'Reports', description: 'Automatic schedule reports' },
      ],
      cta: {
        headline: 'Manage your schedule easily',
        subheadline: 'Download the template and get started today',
        ctaPrimary: 'Download Now',
        ctaSecondary: 'Try Leopardo',
      },
    },
    checklistPaie: {
      hero: {
        headline: 'Payroll Checklist',
        subheadline: 'Ensure payroll compliance with this complete checklist',
        badge: 'Free Guide',
        ctaPrimary: 'Download the Checklist (PDF)',
        ctaSecondary: 'Start free trial',
      },
      stats: [
        { title: '50+ Items', description: 'Complete checks for your payroll' },
        { title: 'Compliance support', description: 'Track your payroll and social deadlines' },
        { title: '100% Free', description: 'Download directly in PDF' },
      ],
      sectionTitle: 'Checklist Sections',
      sections: [
        { title: 'Before Payroll', description: 'Preparation and preliminary checks' },
        { title: 'During Payroll', description: 'Calculations and ongoing checks' },
        { title: 'After Payroll', description: 'Validation and archiving' },
        { title: 'Compliance', description: 'Updates and current changes' },
        { title: 'Security', description: 'Data protection and compliance' },
      ],
      cta: {
        headline: 'Ensure your payroll compliance',
        subheadline: 'Download the checklist and check every item',
        ctaPrimary: 'Download Now',
        ctaSecondary: 'Try Leopardo',
      },
    },
  },
  tr: {
    rhStartup: {
      hero: {
        headline: 'Startup’lar için Kapsamlı İK Rehberi',
        subheadline: 'Startup’ınızda çalışanlarınızı yönetmek için bilmeniz gereken her şey',
        badge: 'Ücretsiz Rehber',
        ctaPrimary: 'Rehberi İndir (PDF)',
        ctaSecondary: 'Ücretsiz deneme',
      },
      stats: [
        { title: '10 Bölüm', description: 'Startup İK yönetiminin tüm yönlerini kapsar' },
        { title: '50+ Sayfa', description: 'Örnekler ve şablonlarla ayrıntılı içerik' },
        { title: '%100 Ücretsiz', description: 'Kayıt gerekmez, doğrudan indirin' },
      ],
      sectionTitle: 'Rehber İçeriği',
      sections: [
        { title: 'Startup İK Temelleri', description: 'İK neden önemlidir ve 3 temel direk' },
        { title: 'İşe Alım ve Onboarding', description: 'Doğru yetenekleri nasıl bulur ve entegre edersiniz' },
        { title: 'Sözleşmeler ve Uyumluluk', description: 'Yasaya uyun ve işletmenizi koruyun' },
        { title: 'Maaş Yönetimi', description: 'Maaş sürecinizi otomatikleştirin ve güvenceye alın' },
        { title: 'İzin ve Devamsızlık Yönetimi', description: 'İzinleri ve devamsızlıkları etkin yönetin' },
        { title: 'Kültür ve Bağlılık', description: 'Güçlü bir kültür oluşturun ve çalışanları bağlayın' },
        { title: 'Araçlar ve Sistemler', description: 'Startup’ınız için doğru araçları seçin' },
        { title: 'Performans Yönetimi', description: 'Çalışanlarınızı değerlendirin ve geliştirin' },
        { title: 'Sağlık ve Güvenlik', description: 'Yasal sorumluluklar ve iyi oluş' },
        { title: 'Büyüme ve Ölçeklenebilirlik', description: 'İK’nızı büyümeye hazırlayın' },
      ],
      cta: {
        headline: 'İK’nızı dönüştürmeye hazır mısınız?',
        subheadline: 'Rehberi indirin ve bugün başlayın',
        ctaPrimary: 'Şimdi İndir',
        ctaSecondary: 'Leopardo’yu dene',
      },
    },
    planningEmployes: {
      hero: {
        headline: 'Çalışan Planlama Şablonu',
        subheadline: 'Ekibinizin planlamasını yönetmek için ücretsiz Excel şablonu',
        badge: 'Ücretsiz Şablon',
        ctaPrimary: 'Şablonu İndir (Excel)',
        ctaSecondary: 'Ücretsiz deneme',
      },
      stats: [
        { title: 'Esnek', description: 'Şablonu ihtiyaçlarınıza göre uyarlayın' },
        { title: 'Kullanımı Kolay', description: 'Karmaşık kurulum gerektirmez' },
        { title: '%100 Ücretsiz', description: 'Doğrudan Excel olarak indirin' },
      ],
      sectionTitle: 'Şablon İçeriği',
      sections: [
        { title: 'Çalışan Sayfası', description: 'Temel bilgilerle çalışanlarınızın listesi' },
        { title: 'Aylık Planlama', description: 'Çalışma günleriyle aylık planlama görünümü' },
        { title: 'Çalışma Saatleri', description: 'Saatleri ve molaları takip edin' },
        { title: 'Raporlar', description: 'Planlama için otomatik raporlar' },
      ],
      cta: {
        headline: 'Planlamanızı kolayca yönetin',
        subheadline: 'Şablonu indirin ve bugün başlayın',
        ctaPrimary: 'Şimdi İndir',
        ctaSecondary: 'Leopardo’yu dene',
      },
    },
    checklistPaie: {
      hero: {
        headline: 'Maaş Kontrol Listesi',
        subheadline: 'Bu kapsamlı kontrol listesiyle maaş uyumluluğunu sağlayın',
        badge: 'Ücretsiz Rehber',
        ctaPrimary: 'Kontrol Listesini İndir (PDF)',
        ctaSecondary: 'Ücretsiz deneme',
      },
      stats: [
        { title: '50+ Madde', description: 'Maaşınız için eksiksiz kontroller' },
        { title: 'Garantili Uyumluluk', description: 'Yürürlükteki tüm düzenlemelere uyun' },
        { title: '%100 Ücretsiz', description: 'Doğrudan PDF olarak indirin' },
      ],
      sectionTitle: 'Kontrol Listesi Bölümleri',
      sections: [
        { title: 'Maaştan Önce', description: 'Hazırlık ve ön kontroller' },
        { title: 'Maaş Sırasında', description: 'Hesaplamalar ve devam eden kontroller' },
        { title: 'Maaştan Sonra', description: 'Doğrulama ve arşivleme' },
        { title: 'Uyumluluk', description: 'Güncellemeler ve yürürlükteki değişiklikler' },
        { title: 'Güvenlik', description: 'Veri koruması ve uyumluluk' },
      ],
      cta: {
        headline: 'Maaş uyumluluğunuzu sağlayın',
        subheadline: 'Kontrol listesini indirin ve her maddeyi kontrol edin',
        ctaPrimary: 'Şimdi İndir',
        ctaSecondary: 'Leopardo’yu dene',
      },
    },
  },
  ar: {
    rhStartup: {
      hero: {
        headline: 'الدليل الشامل للموارد البشرية للشركات الناشئة',
        subheadline: 'كل ما تحتاج معرفته لإدارة موظفيك في شركتك الناشئة',
        badge: 'دليل مجاني',
        ctaPrimary: 'تحميل الدليل (PDF)',
        ctaSecondary: 'تجربة مجانية',
      },
      stats: [
        { title: '10 فصول', description: 'تغطي جميع جوانب إدارة الموارد البشرية في الشركات الناشئة' },
        { title: '+50 صفحة', description: 'محتوى مفصل مع أمثلة وقوالب' },
        { title: 'مجاني 100%', description: 'لا حاجة للتسجيل، حمّل مباشرة' },
      ],
      sectionTitle: 'محتوى الدليل',
      sections: [
        { title: 'أساسيات الموارد البشرية للشركات الناشئة', description: 'لماذا تُعد الموارد البشرية مهمة والركائز الثلاث الأساسية' },
        { title: 'التوظيف والانضمام', description: 'كيف تجد وتدمج المواهب المناسبة' },
        { title: 'العقود والامتثال', description: 'الالتزام بالقانون وحماية شركتك' },
        { title: 'إدارة الرواتب', description: 'أتمتة وتأمين رواتبك' },
        { title: 'إدارة الإجازات والغياب', description: 'إدارة الإجازات والغياب بفعالية' },
        { title: 'الثقافة والمشاركة', description: 'بناء ثقافة قوية وإشراك موظفيك' },
        { title: 'الأدوات والأنظمة', description: 'اختيار الأدوات المناسبة لشركتك الناشئة' },
        { title: 'إدارة الأداء', description: 'تقييم وتطوير موظفيك' },
        { title: 'الصحة والسلامة', description: 'المسؤوليات القانونية والرفاهية' },
        { title: 'النمو وقابلية التوسع', description: 'تجهيز الموارد البشرية للنمو' },
      ],
      cta: {
        headline: 'هل أنت مستعد لتحويل مواردك البشرية؟',
        subheadline: 'حمّل الدليل وابدأ اليوم',
        ctaPrimary: 'حمّل الآن',
        ctaSecondary: 'جرّب ليوناردو',
      },
    },
    planningEmployes: {
      hero: {
        headline: 'قالب جدولة الموظفين',
        subheadline: 'قالب Excel مجاني لإدارة جدولة فريقك',
        badge: 'قالب مجاني',
        ctaPrimary: 'تحميل القالب (Excel)',
        ctaSecondary: 'تجربة مجانية',
      },
      stats: [
        { title: 'مرن', description: 'كيِّف القالب حسب احتياجاتك' },
        { title: 'سهل الاستخدام', description: 'لا يتطلب إعدادًا معقدًا' },
        { title: 'مجاني 100%', description: 'حمّله مباشرة بصيغة Excel' },
      ],
      sectionTitle: 'محتوى القالب',
      sections: [
        { title: 'ورقة الموظفين', description: 'قائمة موظفيك مع المعلومات الأساسية' },
        { title: 'الجدولة الشهرية', description: 'عرض شهري للجدولة مع أيام العمل' },
        { title: 'ساعات العمل', description: 'تتبع الساعات والاستراحات' },
        { title: 'التقارير', description: 'تقارير تلقائية عن الجدولة' },
      ],
      cta: {
        headline: 'أدر جدولتك بسهولة',
        subheadline: 'حمّل القالب وابدأ اليوم',
        ctaPrimary: 'حمّل الآن',
        ctaSecondary: 'جرّب ليوناردو',
      },
    },
    checklistPaie: {
      hero: {
        headline: 'قائمة فحص الرواتب',
        subheadline: 'ضمن امتثال رواتبك مع هذه القائمة الشاملة',
        badge: 'دليل مجاني',
        ctaPrimary: 'تحميل القائمة (PDF)',
        ctaSecondary: 'تجربة مجانية',
      },
      stats: [
        { title: '+50 نقطة', description: 'فحوصات كاملة لرواتبك' },
        { title: 'امتثال مضمون', description: 'التزم بجميع اللوائح السارية' },
        { title: 'مجاني 100%', description: 'حمّلها مباشرة بصيغة PDF' },
      ],
      sectionTitle: 'أقسام القائمة',
      sections: [
        { title: 'قبل الرواتب', description: 'التحضير والفحوصات الأولية' },
        { title: 'أثناء الرواتب', description: 'الحسابات والفحوصات الجارية' },
        { title: 'بعد الرواتب', description: 'التحقق والأرشفة' },
        { title: 'الامتثال', description: 'التحديثات والتغييرات السارية' },
        { title: 'الأمان', description: 'حماية البيانات والامتثال' },
      ],
      cta: {
        headline: 'ضمن امتثال رواتبك',
        subheadline: 'حمّل القائمة وتحقق من كل نقطة',
        ctaPrimary: 'حمّل الآن',
        ctaSecondary: 'جرّب ليوناردو',
      },
    },
  },
}
