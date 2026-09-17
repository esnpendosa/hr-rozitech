# RMIH Platform — Requirements

## Overview

RMIH (Resource Management Integrated Human) adalah platform SaaS multi-tenant untuk manajemen SDM, absensi, tugas, target, KPI, performa, dan laporan operasional yang terintegrasi dalam satu sistem. Platform dibangun dengan Laravel 12 + PostgreSQL + Redis untuk backend, dan Flutter untuk mobile Android. Antarmuka tersedia dalam dua bahasa: **Indonesia** (default) dan **Inggris**, dengan toggle bahasa di seluruh aplikasi (web dan mobile).

---

## Modul 1: Fondasi & Infrastruktur

### 1.1 Setup Project Laravel 12
- GIVEN developer menjalankan fresh install
- WHEN setup project dijalankan
- THEN tersedia project Laravel 12 dengan struktur modular monolith sesuai PRD section 26 (Domain/, Application/, Integrations/, Http/)
- AND file .env dikonfigurasi untuk PostgreSQL, Redis, FCM
- AND semua dependency terinstall (sanctum, spatie/permission, spatie/laravel-auditing, predis, firebase-php-jwt)

### 1.2 Database PostgreSQL & Migrasi
- GIVEN project Laravel terinstall
- WHEN migrasi dijalankan
- THEN semua tabel dari PRD section 28 terbuat dengan kolom, tipe data, foreign key, unique constraint, dan soft delete yang sesuai
- AND setiap tabel yang memiliki relasi tenant menggunakan kolom `tenant_id` UUID
- AND primary key menggunakan ULID/UUID secara konsisten

### 1.3 Indexing Database
- GIVEN tabel-tabel terbuat
- WHEN index diterapkan
- THEN composite index dibuat sesuai PRD section 29 untuk high-volume query patterns (tenant_id+status, tenant_id+employee_id, dll.)

### 1.4 Multi-Tenant Architecture
- GIVEN request masuk ke API
- WHEN middleware dieksekusi
- THEN tenant_id di-resolve dari authenticated context (BUKAN dari request body/query)
- AND semua query otomatis di-scope ke tenant yang aktif melalui Global Scope atau service layer
- AND cross-tenant data access tidak mungkin terjadi untuk role non-super-admin

### 1.5 Sistem Antrian (Queue)
- GIVEN job mahal seperti generate laporan, sinkronisasi fingerprint, kirim notifikasi
- WHEN job di-dispatch
- THEN berjalan secara asinkron melalui Redis queue
- AND tidak pernah dilakukan synchronously dalam HTTP request

---

## Modul 2: Autentikasi & Manajemen Akses

### 2.1 Login & Logout
- GIVEN user dengan email dan password yang valid
- WHEN melakukan login
- THEN menerima token Sanctum yang valid
- AND session/device tercatat di `user_sessions`
- AND audit log dicatat untuk event login

- GIVEN user melakukan logout
- WHEN token di-revoke
- THEN token tidak dapat digunakan kembali
- AND audit log dicatat untuk event logout

### 2.2 Lupa Password
- GIVEN user memasukkan email terdaftar
- WHEN request reset password
- THEN email dengan link reset terkirim
- AND link expired setelah 60 menit

### 2.3 Refresh Token
- GIVEN token yang hampir expired
- WHEN hit endpoint refresh
- THEN token baru diberikan tanpa login ulang

### 2.4 Role & Permission (RBAC)
- GIVEN admin membuat role baru
- WHEN role dibuat dan permission ditetapkan
- THEN user yang diberi role tersebut hanya bisa mengakses fitur sesuai permission
- AND permission check terjadi di service/policy layer, BUKAN hanya di UI

- GIVEN role yang tersedia: Super Admin Platform, Owner, HR Admin, Manager, Supervisor, Employee, Field Worker
- WHEN user mengakses endpoint
- THEN sistem melakukan pengecekan role + permission + kepemilikan data + tenant context

### 2.5 Manajemen Sesi/Device
- GIVEN user login dari multiple device
- WHEN user melihat sesi aktif
- THEN semua sesi tampil dengan info device/IP
- AND user dapat revoke sesi tertentu

### 2.6 Rate Limiting
- GIVEN user mencoba login dengan password salah berulang
- WHEN melebihi 5 percobaan dalam 5 menit
- THEN akun di-throttle sementara
- AND alert dicatat di audit log

---

## Modul 3: Multi-Tenant SaaS

### 3.1 Registrasi Tenant Baru
- GIVEN calon pelanggan mengisi form registrasi
- WHEN form disubmit
- THEN tenant baru dibuat dengan `subdomain/domain` unik
- AND owner account dibuat otomatis
- AND paket STARTER diaktifkan secara default

### 3.2 Manajemen Tenant (Super Admin)
- GIVEN Super Admin login
- WHEN membuka manajemen tenant
- THEN dapat melihat semua tenant, status, paket, dan usage
- AND dapat suspend/activate/delete tenant
- AND dapat login ke tenant untuk support (dengan audit log)

### 3.3 Subscription & Paket
- GIVEN tenant memilih paket
- WHEN paket diaktifkan
- THEN limit sesuai paket diterapkan (employee count, fitur, storage)
- AND paket yang tersedia: STARTER (Rp299rb), BUSINESS (Rp799rb), PROFESSIONAL (Rp1.499rb), ENTERPRISE (custom)

### 3.4 Entitlement & Usage Metering
- GIVEN tenant mencoba menambah employee melebihi limit paket
- WHEN action dieksekusi
- THEN `EntitlementService.canUseFeature()` mengembalikan false
- AND error message informatif ditampilkan dengan saran upgrade
- AND usage dicatat di `usage_counters`

### 3.5 Feature Flags
- GIVEN fitur tertentu dinonaktifkan untuk paket tertentu
- WHEN user mencoba mengakses fitur tersebut
- THEN fitur tidak accessible (bukan hanya disembunyikan di UI)

---

## Modul 4: Organisasi

### 4.1 Manajemen Perusahaan
- GIVEN Owner/HR Admin
- WHEN mengelola profil perusahaan
- THEN dapat mengisi nama, logo, alamat, kontak, NPWP, industri
- AND perubahan dicatat di audit log

### 4.2 Cabang (Branch)
- GIVEN Owner/HR Admin
- WHEN membuat cabang baru
- THEN dapat mengisi nama, kode, alamat, koordinat GPS, PIC
- AND employee dapat di-assign ke cabang

### 4.3 Departemen & Divisi
- GIVEN HR Admin
- WHEN membuat departemen/divisi
- THEN hierarki organisasi tersusun: Perusahaan → Divisi → Departemen → Tim → Posisi

### 4.4 Tim & Posisi
- GIVEN Manager
- WHEN membuat tim
- THEN dapat menambah member dari employee yang ada
- AND posisi/jabatan dapat dikonfigurasi per tenant

### 4.5 Lokasi Kerja
- GIVEN HR Admin
- WHEN mendefinisikan lokasi kerja
- THEN dapat mengisi nama, alamat, koordinat GPS, radius geofence
- AND lokasi digunakan untuk validasi absensi

---

## Modul 5: Manajemen Karyawan

### 5.1 Profil Karyawan
- GIVEN HR Admin
- WHEN menambah karyawan baru
- THEN dapat mengisi: NIK, nama, foto, tempat/tanggal lahir, jenis kelamin, agama, status pernikahan, alamat, kontak, email, cabang, departemen, jabatan, tanggal masuk
- AND ID karyawan auto-generate dengan format konfigurabel (mis. EMP-2025-001)

### 5.2 Status Kepegawaian & Kontrak
- GIVEN HR Admin
- WHEN mengatur status karyawan
- THEN dapat memilih: Tetap, Kontrak, Probation, Part-time, Freelance
- AND tanggal mulai/akhir kontrak dapat diset
- AND notifikasi otomatis ketika kontrak akan berakhir (30/7/1 hari sebelum)

### 5.3 Dokumen Karyawan
- GIVEN HR Admin atau Karyawan
- WHEN mengupload dokumen
- THEN dokumen tersimpan di storage private
- AND tipe dokumen: KTP, NPWP, Ijazah, Sertifikasi, Perjanjian Kerja, dll.
- AND akses via signed URL
- AND validasi MIME, ekstensi, dan ukuran file

### 5.4 Kontak Darurat
- GIVEN HR Admin
- WHEN mengisi kontak darurat
- THEN minimal 1 kontak darurat dapat tersimpan dengan nama, hubungan, nomor telepon

### 5.5 Riwayat Karyawan
- GIVEN perubahan data karyawan terjadi
- WHEN data diubah
- THEN riwayat perubahan tersimpan di `employee_histories` dengan nilai lama dan baru

### 5.6 Pencarian & Filter Karyawan
- GIVEN HR Admin membuka daftar karyawan
- WHEN menggunakan pencarian/filter
- THEN dapat filter berdasarkan: nama, NIK, cabang, departemen, jabatan, status, tanggal masuk

---

## Modul 6: Absensi

### 6.1 Check-in & Check-out Manual (Flutter)
- GIVEN Karyawan membuka aplikasi mobile
- WHEN melakukan check-in
- THEN dicatat: waktu, GPS koordinat, foto selfie (opsional), device ID
- AND sistem memvalidasi geofence jika dikonfigurasi
- AND jika diluar geofence, dicatat sebagai "diluar area" dan butuh approval

### 6.2 Jadwal Kerja & Shift
- GIVEN HR Admin membuat shift
- WHEN shift diassign ke karyawan/tim
- THEN sistem tahu jam kerja, toleransi keterlambatan, dan aturan lembur tiap shift
- AND shift dapat berulang (harian, mingguan) atau custom per tanggal

### 6.3 Riwayat Absensi
- GIVEN Karyawan/Manager/HR
- WHEN melihat riwayat absensi
- THEN tersedia kalender dan list view dengan status: Hadir, Terlambat, Alpha, Izin, Cuti, Lembur
- AND rekapitulasi per periode (harian, mingguan, bulanan)

### 6.4 Koreksi Absensi
- GIVEN Karyawan mengajukan koreksi absensi
- WHEN submission dibuat
- THEN Manager/HR menerima notifikasi untuk approval
- AND alur: Draft → Submitted → Approved/Rejected
- AND audit log mencatat setiap perubahan

### 6.5 Izin & Cuti
- GIVEN Karyawan mengajukan izin/cuti
- WHEN submission dibuat
- THEN Manager/HR menerima notifikasi
- AND saldo cuti ter-update otomatis setelah approved
- AND jenis cuti dapat dikonfigurasi: Tahunan, Sakit, Melahirkan, Duka, dll.

### 6.6 Lembur
- GIVEN Karyawan mencatat lembur
- WHEN submission dibuat
- THEN Manager approve/reject
- AND durasi lembur tercatat untuk keperluan laporan

### 6.7 Dashboard Absensi
- GIVEN Manager/HR membuka dashboard absensi
- WHEN hari ini
- THEN tampil: total hadir, total alpha, terlambat, dalam izin, serta daftar karyawan yang belum absen

---

## Modul 7: Fingerprint Integration

### 7.1 Registrasi Perangkat
- GIVEN HR Admin mendaftarkan fingerprint device
- WHEN device didaftarkan
- THEN tersimpan: nama, model, serial number, IP/URL, lokasi, status aktif
- AND koneksi ke device dapat di-test

### 7.2 Provider Abstraction
- GIVEN sistem menggunakan fingerprint device X Solutions
- WHEN sync dilakukan
- THEN melalui `FingerprintProvider` interface (bukan hard-code X Solutions)
- AND implementasi X Solutions di `app/Integrations/Fingerprint/XSolutions/`

### 7.3 Mapping Karyawan-Device
- GIVEN HR Admin mengatur mapping
- WHEN karyawan di-assign ke device
- THEN fingerprint device tahu ID karyawan yang terdaftar

### 7.4 Sinkronisasi Absensi
- GIVEN sync berjalan (manual atau terjadwal)
- WHEN attendance log ditarik dari device
- THEN data disimpan ke `attendance_events` dengan proteksi duplikat (idempotent)
- AND `source`, `source_device_id`, `source_event_id` tercatat
- AND unique constraint mencegah import duplikat

### 7.5 Sync Scheduling & Retry
- GIVEN sync gagal karena network/device issue
- WHEN retry dijalankan
- THEN sistem mencoba ulang dengan exponential backoff
- AND error dicatat di `fingerprint_sync_errors`
- AND last_successful_sync ter-update

### 7.6 Sync Dashboard
- GIVEN HR Admin melihat sync logs
- WHEN membuka halaman Fingerprint
- THEN tampil: status setiap device, last sync time, jumlah record synced, error log

---

## Modul 8: Manajemen Tugas (Task)

### 8.1 Buat & Assign Tugas
- GIVEN Manager/Supervisor membuat tugas
- WHEN form disubmit
- THEN tugas tersimpan dengan: judul, deskripsi, assignee, deadline, prioritas (Low/Medium/High/Critical), project (opsional), customer (opsional)
- AND assignee menerima push notification dan in-app notification

### 8.2 Status Tugas (Lifecycle)
- GIVEN tugas berjalan
- WHEN status diupdate
- THEN mengikuti alur: Draft → Assigned → Accepted → In Progress → Waiting → Review → Completed
- AND terminal states: Rejected, Cancelled, Overdue
- AND setiap perubahan status dicatat di `task_status_histories`

### 8.3 Checklist Tugas
- GIVEN tugas memiliki checklist
- WHEN karyawan menyelesaikan checklist item
- THEN progress otomatis ter-update
- AND `completed_by` dan `completed_at` tercatat

### 8.4 Evidence (Bukti Pekerjaan)
- GIVEN karyawan mengunggah evidence
- WHEN file diupload
- THEN tersimpan dengan: tipe (foto/video/dokumen), deskripsi, timestamp, koordinat GPS (untuk field work)
- AND file tersimpan di storage private dengan validasi MIME dan ukuran

### 8.5 Komentar Tugas
- GIVEN user membuka detail tugas
- WHEN menambah komentar
- THEN komentar tersimpan dengan timestamp dan author
- AND mention (@user) menghasilkan notifikasi

### 8.6 Kanban Board
- GIVEN Manager/Supervisor membuka Task Kanban
- WHEN membuka view Kanban
- THEN tugas tampil dalam kolom sesuai status
- AND drag-and-drop untuk mengubah status tugas

### 8.7 Tugas Overdue
- GIVEN tugas melewati deadline
- WHEN sistem mendeteksi
- THEN status berubah ke Overdue secara otomatis (via scheduled job)
- AND notifikasi dikirim ke assignee dan supervisor

### 8.8 Filter & Pencarian Tugas
- GIVEN user membuka daftar tugas
- WHEN menggunakan filter
- THEN dapat filter: status, prioritas, assignee, project, deadline range, overdue only

---

## Modul 9: Manajemen Proyek

### 9.1 Buat Proyek
- GIVEN Manager membuat proyek
- WHEN form disubmit
- THEN proyek tersimpan dengan: kode, nama, deskripsi, PM, anggota, customer, start, deadline, target, status

### 9.2 Anggota Proyek
- GIVEN Manager mengelola anggota
- WHEN anggota ditambah/dihapus
- THEN anggota menerima notifikasi
- AND anggota dapat melihat semua tugas dalam proyek

### 9.3 Progress Proyek
- GIVEN tugas dalam proyek diupdate
- WHEN progress berubah
- THEN progress proyek di-aggregate (bukan real-time recalculate tiap request)
- AND nilai di-cache dan diperbarui secara background

### 9.4 Dashboard Proyek
- GIVEN Manager melihat proyek
- WHEN membuka detail proyek
- THEN tampil: status, progress %, overdue tasks, team performance, timeline

---

## Modul 10: Target Management

### 10.1 Definisi Target
- GIVEN Manager mendefinisikan target
- WHEN target dibuat
- THEN tersimpan: nama, metrik, nilai target, satuan, periode, start date, end date, assignee (employee/team/project)

### 10.2 Update Progress Target
- GIVEN Karyawan/Manager mengisi progress
- WHEN nilai aktual diperbarui
- THEN sistem otomatis menghitung: % achievement, remaining, required daily rate
- AND risk level di-assess: ON_TRACK, AT_RISK, CRITICAL, COMPLETED, EXPIRED

### 10.3 Risk Assessment Target
- GIVEN sistem mengevaluasi target
- WHEN status dihitung
- THEN formula risk configurable (bukan hard-coded)
- AND notifikasi dikirim ketika target masuk status AT_RISK atau CRITICAL

### 10.4 Target Dashboard
- GIVEN Manager/Owner membuka target
- WHEN melihat overview
- THEN tampil: semua target aktif, achievement %, risk level, trend chart

### 10.5 Periode Target
- GIVEN target memiliki periode
- WHEN periode berakhir
- THEN target otomatis expired jika belum completed
- AND historical data tetap tersimpan untuk laporan

---

## Modul 11: KPI Engine

### 11.1 Template KPI
- GIVEN HR Admin/Manager membuat template KPI
- WHEN template dibuat
- THEN dapat mendefinisikan: nama metrik, weight (%), target, minimum, maximum, scoring method
- AND total weight harus 100% untuk template yang aktif

### 11.2 Assignment KPI
- GIVEN Manager melakukan assign KPI
- WHEN KPI di-assign ke employee
- THEN karyawan tahu target KPI untuk periode tersebut

### 11.3 Input Aktual KPI
- GIVEN sistem/manager menginput nilai aktual
- WHEN nilai dimasukkan
- THEN score dihitung berdasarkan scoring method yang dikonfigurasi
- AND scoring method configurable (bukan formula fixed)

### 11.4 Hasil KPI
- GIVEN periode KPI selesai
- WHEN hasil dihitung
- THEN KPI score karyawan tersimpan di `kpi_results`
- AND tidak berubah retroaktif meski konfigurasi berubah (versioning)

---

## Modul 12: Performance Engine

### 12.1 Konfigurasi Performance
- GIVEN HR Admin mengonfigurasi formula performa
- WHEN bobot dikonfigurasi
- THEN dapat mengatur proporsi: KPI score, task achievement, target achievement, attendance indicators
- AND konfigurasi tersimpan dengan versioning

### 12.2 Kalkulasi Performance
- GIVEN periode performa berakhir
- WHEN kalkulasi dijalankan
- THEN skor performa karyawan dihitung berdasarkan konfigurasi aktif
- AND hasil tersimpan di `performance_results` dan tidak berubah retroaktif

### 12.3 Dashboard Performance
- GIVEN Manager/Owner melihat performance
- WHEN membuka halaman performance
- THEN tampil: top performer, bottom performer, trend per periode, department comparison

---

## Modul 13: Customer & Field Work

### 13.1 Manajemen Customer
- GIVEN Sales/Manager menambah customer
- WHEN customer dibuat
- THEN tersimpan: kode, nama, perusahaan, email, phone, alamat, koordinat GPS, status, assigned team

### 13.2 Field Job
- GIVEN Technician/Field Worker menerima job lapangan
- WHEN job di-assign
- THEN karyawan menerima notifikasi
- AND job berisi: customer, site, koordinat, jadwal, checklist, notes

### 13.3 Eksekusi Job Lapangan
- GIVEN Field Worker di lapangan
- WHEN mengerjakan job
- THEN dapat: check-in di lokasi (GPS), isi checklist, upload foto evidence, isi work notes, rekam tanda tangan customer

### 13.4 Offline Support (Flutter)
- GIVEN Field Worker tanpa koneksi
- WHEN mengisi data job lapangan
- THEN data tersimpan di local queue
- AND sync otomatis ketika koneksi kembali

---

## Modul 14: Notifikasi & Push Notification (FCM)

### 14.1 Firebase Cloud Messaging Setup
- GIVEN sistem siap kirim notifikasi
- WHEN notifikasi di-trigger
- THEN menggunakan FCM (gratis) untuk push notification ke Flutter Android dan Web
- AND setiap user dapat menyimpan FCM token di `user_sessions`

### 14.2 In-App Notification
- GIVEN event terjadi (tugas baru, target warning, dll.)
- WHEN notifikasi di-generate
- THEN tersimpan di tabel `notifications` dengan: tenant_id, user_id, type, title, body, data, read_at
- AND badge count ter-update realtime

### 14.3 Push Notification (FCM)
- GIVEN notifikasi in-app dibuat
- WHEN user memiliki FCM token
- THEN push notification dikirim via FCM secara async melalui queue
- AND retry otomatis jika FCM gagal

### 14.4 Tipe Notifikasi
- Tugas baru di-assign
- Deadline tugas mendekati (1 hari/3 jam sebelum)
- Tugas overdue
- Target AT_RISK atau CRITICAL
- Absensi issue (belum check-in padahal sudah jam kerja)
- Cuti/izin disubmit (untuk approver)
- Cuti/izin approved/rejected
- Koreksi absensi disubmit (untuk approver)
- Pengumuman dari management

### 14.5 Preferensi Notifikasi
- GIVEN user membuka pengaturan notifikasi
- WHEN mengubah preferensi
- THEN dapat toggle on/off per tipe notifikasi
- AND preferensi tersimpan di `notification_preferences`

### 14.6 Realtime Badge & Count
- GIVEN notifikasi baru masuk
- WHEN user aktif di aplikasi
- THEN badge/counter notifikasi ter-update tanpa page refresh (menggunakan Laravel Broadcasting + FCM atau polling interval)

---

## Modul 15: Laporan & Ekspor

### 15.1 Laporan Absensi
- GIVEN HR/Manager membuka laporan absensi
- WHEN filter diterapkan (periode, cabang, karyawan)
- THEN tampil rekapitulasi: hadir, alpha, terlambat, izin, cuti, lembur
- AND dapat diekspor ke CSV, XLSX, PDF

### 15.2 Laporan Tugas
- GIVEN Manager membuka laporan tugas
- WHEN filter diterapkan
- THEN tampil: completion rate, overdue %, distribusi per status, per assignee, per project

### 15.3 Laporan Target
- GIVEN Manager/Owner membuka laporan target
- WHEN filter diterapkan
- THEN tampil: achievement %, risk distribution, trend per periode

### 15.4 Laporan KPI & Performance
- GIVEN Manager/Owner membuka laporan performa
- WHEN filter diterapkan
- THEN tampil: KPI score per karyawan, performance ranking, trend

### 15.5 Ekspor Asinkron
- GIVEN user merequest ekspor file besar
- WHEN ekspor di-submit
- THEN job masuk ke queue
- AND notifikasi dikirim ketika file siap didownload
- AND file tersimpan di storage dengan TTL (auto-delete setelah N hari)

---

## Modul 16: Audit Log

### 16.1 Pencatatan Event
- GIVEN event sensitif terjadi
- WHEN terjadi: login/logout/failed login, perubahan role/permission, CRUD employee, koreksi absensi, perubahan target/KPI, perubahan setting
- THEN audit log dibuat dengan: tenant_id, actor_id, action, entity_type, entity_id, old_values, new_values, IP, user_agent, request_id, timestamp

### 16.2 Audit Log Append-Only
- GIVEN audit log sudah tercatat
- WHEN user biasa mencoba mengubah/menghapus
- THEN tidak diizinkan (append-only dari role normal)

### 16.3 Tampilan Audit Log
- GIVEN Super Admin/Owner membuka audit logs
- WHEN memfilter
- THEN dapat filter berdasarkan: actor, action type, entity type, periode, IP address

---

## Modul 17: Dashboard

### 17.1 Owner Dashboard
- GIVEN Owner login
- WHEN membuka dashboard
- THEN tampil cards: total karyawan, hadir hari ini, tidak hadir, tugas aktif, tugas overdue, % target tercapai, rata-rata KPI
- AND charts: trend absensi, trend tugas, trend target, perbandingan departemen/cabang
- AND alerts: tugas overdue, target AT_RISK, pending approvals

### 17.2 Manager Dashboard
- GIVEN Manager login
- WHEN membuka dashboard
- THEN tampil: kondisi tim hari ini, tugas hari ini, target, absensi tim, KPI, pending review

### 17.3 Employee Dashboard (Mobile)
- GIVEN Karyawan login ke Flutter
- WHEN membuka dashboard
- THEN tampil: tombol check-in/out, tugas hari ini, target, progress, notifikasi terbaru

---

## Modul 18: Landing Page

### 18.1 Struktur Landing Page
- GIVEN visitor mengakses halaman utama
- WHEN halaman dimuat
- THEN tampil sections: Hero, Fitur, Preview Produk, Cara Kerja, Mobile App, Keamanan, Harga, CTA
- AND konten dalam Bahasa Indonesia (default) dengan toggle Bahasa Inggris

### 18.2 Hero Section
- Headline: "Kelola Manusia, Pekerjaan, Target & Performa dengan Mudah"
- Subheadline: deskripsi RMIH dalam ID/EN
- CTA buttons: "Mulai Sekarang" / "Start Now", "Lihat Demo" / "View Demo"

### 18.3 Halaman Harga
- GIVEN visitor melihat halaman harga
- WHEN halaman dimuat
- THEN tampil 4 paket: STARTER, BUSINESS, PROFESSIONAL, ENTERPRISE dengan fitur dan harga masing-masing
- AND dalam Bahasa Indonesia dan Inggris

---

## Modul 19: Bilingual (Indonesia / Inggris)

### 19.1 Web Admin Bilingual
- GIVEN user web admin
- WHEN mengklik toggle bahasa (ID/EN)
- THEN seluruh antarmuka berubah ke bahasa yang dipilih
- AND preferensi bahasa disimpan di localStorage/cookie
- AND semua label, placeholder, pesan error, notifikasi, dan konten UI ter-translate

### 19.2 Flutter Mobile Bilingual
- GIVEN user di aplikasi Flutter
- WHEN memilih bahasa di Settings
- THEN seluruh app berubah ke bahasa pilihan (ID/EN)
- AND menggunakan Flutter i18n/l10n (ARB files)
- AND preferensi tersimpan secara lokal

### 19.3 Coverage Translate
- Semua halaman web (landing page, auth, semua modul admin)
- Semua screen Flutter
- Semua pesan error dan validasi
- Semua email template
- Semua push notification text

---

## Modul 20: Keamanan & Privasi

### 20.1 API Security
- GIVEN semua endpoint API
- WHEN request masuk
- THEN dilindungi dari: broken object-level authorization, mass assignment, injection, SSRF, excessive data exposure
- AND setiap endpoint membutuhkan authorization check

### 20.2 File Upload Security
- GIVEN user mengupload file
- WHEN file diterima
- THEN divalidasi: MIME type, ekstensi, ukuran, file signature
- AND disimpan di luar public web root
- AND diakses via signed URL dengan expiry

### 20.3 Rate Limiting
- GIVEN API endpoint publik
- WHEN traffic tinggi atau abuse terdeteksi
- THEN rate limiting diterapkan (berbeda per endpoint: auth lebih ketat, API umum lebih longgar)

### 20.4 HTTPS & Enkripsi
- GIVEN data sensitif karyawan tersimpan
- WHEN diakses
- THEN selalu melalui HTTPS
- AND secrets tersimpan di environment variables (tidak di code)
- AND backup database terenkripsi

---

## Modul 21: Flutter Mobile App

### 21.1 Arsitektur Flutter
- GIVEN project Flutter dibuat
- WHEN struktur dibuat
- THEN menggunakan feature-oriented structure sesuai PRD section 25 (lib/core/, lib/features/, lib/shared/)
- AND menggunakan repository pattern, service layer, centralized API client
- AND token tersimpan di secure storage (flutter_secure_storage)

### 21.2 Screen Flutter
- Splash screen dengan logo RMIH
- Login & Forgot Password
- Home/Dashboard dengan tombol check-in/out prominenmt
- Daftar & Detail Tugas
- Checklist & Evidence upload
- Target & Progress
- Notifikasi
- Profil & Pengaturan (termasuk toggle bahasa)
- Jadwal
- Customer & Field Job
- Offline sync indicator

### 21.3 FCM Integration (Flutter)
- GIVEN Flutter app teinstall
- WHEN app dibuka pertama kali
- THEN meminta permission notifikasi
- AND FCM token di-register ke backend
- AND notifikasi push tampil bahkan ketika app di-background/closed

### 21.4 Offline Capability
- GIVEN field worker tanpa koneksi
- WHEN mengisi data job lapangan
- THEN data di-queue secara lokal
- AND sync indicator tampil
- AND data tersync otomatis ketika online

---

## Catatan Tambahan

- Semua response API menggunakan envelope standar: `{success, data, meta}` untuk sukses, `{success, message, errors}` untuk error
- Pagination wajib untuk semua endpoint yang mengembalikan list
- Tenant ID tidak pernah diterima dari client untuk operasi bisnis
- Performance calculation harus versioned (tidak berubah retroaktif)
- Semua job mahal (ekspor, sync) melalui queue, tidak synchronous
- i18n keys harus konsisten antara web dan dokumentasi
