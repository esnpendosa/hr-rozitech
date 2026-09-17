# HR Rozitech Platform (RMIH)
## Resource Management Integrated Human — Enterprise B2B SaaS Platform

[![Tests](https://img.shields.io/badge/tests-57%20passed-brightgreen.svg)]()
[![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)]()
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16%2B-blue.svg)]()
[![Redis](https://img.shields.io/badge/Redis-5.0%2B-red.svg)]()
[![License](https://img.shields.io/badge/license-Commercial-blue.svg)]()

Platform Enterprise Human Resource, Workforce Management, Target Tracking, KPI & Performance Management, serta Hardware Biometrik yang terintegrasi secara modular dengan AI-Ready architecture.

---

## 🌟 Fitur Utama (Core Modules)

### 1. Struktur Organisasi & Hierarki Perusahaan
- **Organization Hub (`/organization`)**: Ringkasan data badan usaha, kantor cabang, divisi, jabatan, tim kerja, dan sebaran SDM.
- **Pengaturan Profil Perusahaan (`/settings/company`)**: Konfigurasi identitas perusahaan, legalitas, domisili, email, website, dan nomor kontak.
- **Kantor Cabang (`/branches`)**: Pendaftaran cabang operasional dengan alamat, PIC/manager cabang, nomor telepon, dan status unit.
- **Departemen & Divisi (`/departments`)**: Pengelompokan struktur organisasi fungsional dan pengaitan ke kantor cabang.
- **Jabatan & Posisi (`/positions`)**: Pengaturan jenjang karier (Level 1–20), peran kerja, dan spesialisasi fungsional.
- **Tim Kerja & Squad (`/teams`)**: Manajemen regu kerja lintas departemen untuk pelaksanaan proyek khusus.
- **Lokasi Kerja & Geofencing (`/work-locations`)**: Penentuan titik koordinat presensi GPS (Latitude/Longitude) dengan batas radius (*geofencing radius*).

### 2. Manajemen Karyawan & Dokumen SDM
- **Database Karyawan (`/employees`)**: Profil lengkap pegawai, nomor induk (NIK), nomor pegawai, foto, dan status kerja (Permanent, Contract, Probation, Freelance).
- **Kontrak & Dokumen (`/employees/{id}`)**: Pelacakan riwayat kontrak kerja, surat peringatan (SP), mutasi, kontak darurat, serta upload dokumen identitas.

### 3. Kehadiran, Shift & Mesin Absensi Biometrik
- **Presensi GPS & Geofencing (`/attendance`)**: Pencatatan clock-in / clock-out dengan validasi radius lokasi kerja dan deteksi anomali.
- **Manajemen Shift & Jadwal**: Konfigurasi jadwal kerja fleksibel, rotasi shift mingguan, serta cuti bersama.
- **Pengajuan Izin, Cuti & Lembur (`/attendance/leaves`)**: Alur persetujuan (*approval workflow*) berjenjang oleh manajer/HRD.
- **Koreksi Absensi (`/attendance/corrections`)**: Mekanisme banding kehadiran pegawai saat lupa melakukan check-in/out.
- **Integrasi Mesin Fingerprint (`/fingerprint`)**:
  - Dukungan perangkat fisik ZKTeco, Solution, Fingerspot, dan XSolutions (Port 4370).
  - Monitoring status koneksi online/offline perangkat.
  - Penarikan log absensi (*log synchronization*) manual maupun terjadwal via background worker.
  - Pemetaan PIN sidik jari ke akun pegawai (`/fingerprint/mappings`).

### 4. Manajemen Tugas, Proyek & Sasaran Kinerja
- **Tugas & Kanban Board (`/tasks`, `/tasks/kanban`)**: Pemantauan siklus hidup tugas (To Do, In Progress, Review, Done) dengan checklist dan bukti pengerjaan (*evidence attachment*).
- **Manajemen Proyek (`/projects`)**: Pelacakan milestone, anggaran biaya, dan anggota tim lintas divisi.
- **Target Kinerja & Sasaran (`/targets`)**:
  - Perhitungan laju harian otomatis (*required daily pace*) untuk mencapai target kuantitatif tepat waktu.
  - Analisis risiko ketercapaian (Aman, Waspada, Berisiko Tinggi).
- **KPI & Evaluasi Kinerja (`/kpi`)**:
  - Template KPI fleksibel dengan validasi total bobot 100% (preset standar 50% Hasil / 25% Proses / 25% Perilaku).
  - Skoring otomatis capaian kinerja bulanan, triwulanan, dan tahunan.

### 5. Multi-Tenant SaaS, Keamanan & Billing
- **Isolasi Tenant Otomatis**: Setiap data perusahaan terisolasi penuh berbasis `tenant_id` dan context resolver.
- **Registrasi Mandiri & Langganan**: Alur pendaftaran tenant baru, pemilihan paket (Starter, Pro, Enterprise), dan masa uji coba (*trial*).
- **Sistem Kunci Otomatis (*Lockout Protection*)**: Proteksi akses saat kuota pegawai terlampaui atau masa aktif paket kedaluwarsa.
- **Audit Logging**: Pencatatan riwayat setiap aksi dan modifikasi data krusial untuk kebutuhan audit internal.

### 6. AI Assistant Terintegrasi
- **Asisten Percakapan AI (`/ai/assistant`)**: Didukung model OpenRouter dan *enterprise knowledge service* untuk menjawab pertanyaan operasional, analisis kehadiran, dan ringkasan produktivitas tim.
- **Sinkronisasi Waktu Real-Time**: Jam kerja dan timestamp diselaraskan ke Waktu Indonesia Barat (`Asia/Jakarta` - WIB).

---

## 🛠️ Persyaratan Sistem (System Requirements)

- **PHP**: 8.2 atau 8.3+ (ekstensi: `pdo_pgsql`, `redis`, `mbstring`, `openssl`, `curl`, `gd`, `bcmath`)
- **Web Server**: Nginx / Apache / Laragon
- **Database**: PostgreSQL 15 / 16+
- **Cache & Queue**: Redis 5.0+
- **Composer**: 2.x+
- **Node.js**: 18+ (Opsional jika ingin build aset frontend)

---

## 🚀 Panduan Instalasi Lokal (Quickstart)

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/esnpendosa/hr-rozitech.git
   cd hr-rozitech
   ```

2. **Pasang Dependensi PHP**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Pastikan konfigurasi PostgreSQL dan Redis telah sesuai di file `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=rmih
   DB_USERNAME=postgres
   DB_PASSWORD=your_password

   REDIS_CLIENT=phpredis
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379

   APP_TIMEZONE=Asia/Jakarta
   ```

4. **Jalankan Migrasi & Database Seeder**:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Server Pengembangan**:
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser di `http://127.0.0.1:8000`.

---

## 🧪 Pengujian Otomatis (Automated Tests)

Platform dilengkapi pengujian otomatis lengkap (Feature & Unit Tests) yang mencakup seluruh alur kerja operasional:
```bash
php artisan test
```
*Status: 57 passed (306 assertions)*

---

## 📁 Struktur Dokumen Terkait
- [Spesifikasi Lengkap Produk (Master PRD)](./RMIH_PRD_Master_Kiro_AI.md)
- [Panduan Aplikasi Mobile Flutter](./rmih_flutter/README.md)

---

## 📄 Lisensi
Hak Cipta &copy; 2026 PT Rozitech Technology. Seluruh hak cipta dilindungi.

