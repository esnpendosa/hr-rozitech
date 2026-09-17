# RMIH Platform — Design

## Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 12 (PHP 8.3+) |
| Database | PostgreSQL 16 |
| Cache / Queue | Redis |
| Auth | Laravel Sanctum |
| Permission | spatie/laravel-permission |
| Audit | spatie/laravel-auditing |
| Push Notification | Firebase Cloud Messaging (FCM) — gratis |
| Realtime Web | Laravel Broadcasting + Redis (Pusher-compatible) |
| Storage | Local (dev) / S3-compatible (prod) |
| Queue Worker | Laravel Horizon atau supervisor |
| Frontend Web | Blade + Alpine.js + Tailwind CSS (atau Laravel Breeze/Livewire) |
| Mobile | Flutter (Dart) |
| i18n Web | Laravel lang files (resources/lang/id, resources/lang/en) |
| i18n Flutter | Flutter ARB / flutter_localizations |

---

## Arsitektur Backend (Modular Monolith)

```
app/
├── Domain/
│   ├── Tenant/
│   │   ├── Models/Tenant.php
│   │   ├── Models/TenantSetting.php
│   │   └── Services/TenantService.php
│   ├── Organization/
│   │   ├── Models/ (Company, Branch, Department, Division, Team, Position, WorkLocation)
│   │   └── Services/OrganizationService.php
│   ├── Employee/
│   │   ├── Models/ (Employee, EmployeeContract, EmployeeDocument, EmployeeEmergencyContact, EmployeeHistory)
│   │   └── Services/EmployeeService.php
│   ├── Attendance/
│   │   ├── Models/ (AttendanceRecord, AttendanceEvent, AttendanceCorrection, LeaveType, LeaveRequest, OvertimeRequest, WorkShift, WorkSchedule)
│   │   └── Services/AttendanceService.php
│   ├── Fingerprint/
│   │   ├── Models/ (FingerprintDevice, FingerprintDeviceUser, FingerprintSyncRun, FingerprintSyncError, FingerprintMapping)
│   │   └── Services/FingerprintSyncService.php
│   ├── Task/
│   │   ├── Models/ (Task, TaskAssignee, TaskChecklist, TaskComment, TaskAttachment, TaskEvidence, TaskStatusHistory)
│   │   └── Services/TaskService.php
│   ├── Project/
│   │   ├── Models/ (Project, ProjectMember)
│   │   └── Services/ProjectService.php
│   ├── Target/
│   │   ├── Models/ (Target, TargetAssignment, TargetProgress, TargetPeriod)
│   │   └── Services/TargetService.php
│   ├── KPI/
│   │   ├── Models/ (KpiTemplate, KpiMetric, KpiAssignment, KpiResult)
│   │   └── Services/KpiService.php
│   ├── Performance/
│   │   ├── Models/ (PerformancePeriod, PerformanceResult)
│   │   └── Services/PerformanceService.php
│   ├── Customer/
│   │   ├── Models/ (Customer, CustomerContact, CustomerLocation, FieldJob)
│   │   └── Services/CustomerService.php
│   ├── Notification/
│   │   ├── Models/ (Notification, NotificationPreference, NotificationLog)
│   │   └── Services/NotificationService.php
│   ├── Report/
│   │   └── Services/ReportService.php
│   └── SaaS/
│       ├── Models/ (SubscriptionPlan, Subscription, UsageCounter, PlanFeature)
│       └── Services/EntitlementService.php
│
├── Application/
│   ├── Actions/          ← single-purpose action classes
│   ├── Services/         ← cross-domain services
│   └── DTOs/             ← data transfer objects
│
├── Integrations/
│   └── Fingerprint/
│       ├── Contracts/FingerprintProvider.php   ← interface
│       └── XSolutions/XSolutionsProvider.php  ← implementasi vendor
│
├── Http/
│   ├── Controllers/
│   │   ├── Api/V1/       ← API controllers
│   │   └── Web/          ← web controllers
│   ├── Requests/         ← Form Request validation
│   ├── Resources/        ← API Resource transformers
│   └── Middleware/
│       ├── ResolveTenant.php
│       ├── CheckEntitlement.php
│       └── SetLocale.php
│
└── Console/
    └── Commands/         ← scheduled jobs, sync commands
```

---

## Arsitektur Flutter

```
lib/
├── core/
│   ├── config/           ← app config, env
│   ├── network/          ← Dio HTTP client, interceptors
│   ├── auth/             ← auth state, token management
│   ├── storage/          ← flutter_secure_storage
│   ├── permissions/      ← device permissions handler
│   ├── notifications/    ← FCM setup, handler
│   ├── i18n/             ← localization setup
│   └── theme/            ← colors, typography, brand
│
├── features/
│   ├── auth/             ← login, forgot password
│   ├── dashboard/        ← home screen
│   ├── attendance/       ← check-in, check-out, history
│   ├── tasks/            ← task list, detail, kanban
│   ├── targets/          ← target list, progress
│   ├── notifications/    ← notification center
│   ├── profile/          ← profile, settings, language toggle
│   ├── customers/        ← customer list, detail
│   └── field_work/       ← field job, checklist, evidence
│
└── shared/
    ├── widgets/          ← reusable UI components
    ├── models/           ← shared data models
    └── utils/            ← helpers, formatters
```

---

## Database Schema (Key Tables)

### tenants
```sql
id          UUID PK
name        VARCHAR(255)
slug        VARCHAR(100) UNIQUE
domain      VARCHAR(255) UNIQUE NULL
status      ENUM('active','suspended','trial')
plan_id     UUID FK subscription_plans
created_at, updated_at, deleted_at
```

### users
```sql
id          UUID PK
tenant_id   UUID FK tenants (NULL untuk platform admin)
name        VARCHAR(255)
email       VARCHAR(255) UNIQUE
password    VARCHAR(255)
fcm_token   TEXT NULL
locale      VARCHAR(10) DEFAULT 'id'
created_at, updated_at, deleted_at
```

### employees
```sql
id              UUID PK
tenant_id       UUID FK tenants
user_id         UUID FK users NULL
employee_number VARCHAR(50)
name            VARCHAR(255)
nik             VARCHAR(20)
birth_date      DATE NULL
gender          ENUM('male','female')
branch_id       UUID FK branches
department_id   UUID FK departments
position_id     UUID FK positions
employment_type ENUM('permanent','contract','probation','part_time','freelance')
join_date       DATE
status          ENUM('active','inactive','resigned')
created_at, updated_at, deleted_at
INDEX (tenant_id, status)
INDEX (tenant_id, branch_id)
```

### attendance_records
```sql
id              UUID PK
tenant_id       UUID FK
employee_id     UUID FK employees
attendance_date DATE
check_in_at     TIMESTAMP NULL
check_out_at    TIMESTAMP NULL
check_in_lat    DECIMAL(10,7) NULL
check_in_lng    DECIMAL(10,7) NULL
status          ENUM('present','absent','late','permission','leave','holiday')
source          ENUM('mobile','fingerprint','manual')
source_device_id VARCHAR(100) NULL
source_event_id  VARCHAR(255) NULL
created_at, updated_at
UNIQUE (tenant_id, employee_id, attendance_date, source_event_id)
INDEX (tenant_id, employee_id, attendance_date)
```

### tasks
```sql
id              UUID PK
tenant_id       UUID FK
project_id      UUID FK projects NULL
customer_id     UUID FK customers NULL
assigned_by     UUID FK users
priority        ENUM('low','medium','high','critical')
title           VARCHAR(500)
description     TEXT NULL
status          ENUM('draft','assigned','accepted','in_progress','waiting','review','completed','rejected','cancelled','overdue')
start_at        TIMESTAMP NULL
due_at          TIMESTAMP NULL
completed_at    TIMESTAMP NULL
progress_value  DECIMAL(10,2) DEFAULT 0
progress_percent DECIMAL(5,2) DEFAULT 0
created_by      UUID FK users
updated_by      UUID FK users
created_at, updated_at, deleted_at
INDEX (tenant_id, status)
INDEX (tenant_id, due_at, status)
```

### targets
```sql
id              UUID PK
tenant_id       UUID FK
name            VARCHAR(255)
metric          VARCHAR(100)
target_value    DECIMAL(15,2)
unit            VARCHAR(50)
period          ENUM('daily','weekly','monthly','quarterly','annual','custom')
start_date      DATE
end_date        DATE
actual_value    DECIMAL(15,2) DEFAULT 0
progress_percent DECIMAL(5,2) DEFAULT 0
remaining_value DECIMAL(15,2)
required_daily_rate DECIMAL(15,2)
risk_level      ENUM('on_track','at_risk','critical','completed','expired')
status          ENUM('created','assigned','active','completed','expired')
created_at, updated_at
INDEX (tenant_id, status, end_date)
```

### notifications
```sql
id          UUID PK
tenant_id   UUID FK
user_id     UUID FK users
type        VARCHAR(100)
title       VARCHAR(255)
body        TEXT
data        JSONB NULL
read_at     TIMESTAMP NULL
sent_via_fcm BOOLEAN DEFAULT false
created_at
INDEX (tenant_id, user_id, read_at)
```

---

## Notification Architecture (FCM)

```
Event Terjadi (mis. Task Assigned)
    │
    ▼
NotificationService.send()
    │
    ├─► Simpan ke tabel notifications (in-app)
    │
    └─► Dispatch SendFcmNotificationJob ke queue
            │
            ▼
        FCM API (gratis)
            │
            ├─► Flutter Android → push notification
            └─► Web (optional FCM web push)
```

**Setup FCM:**
- Gratis, tidak ada biaya
- Gunakan `kreait/firebase-php` atau `google/apiclient`
- FCM token disimpan per device di `user_sessions` atau `users.fcm_tokens` (bisa multiple device)
- Service account credentials dari Firebase Console (JSON file, simpan di storage/app/firebase/)

---

## i18n Architecture

### Web (Laravel)
```
resources/
└── lang/
    ├── id/
    │   ├── auth.php
    │   ├── employee.php
    │   ├── attendance.php
    │   ├── task.php
    │   ├── target.php
    │   ├── kpi.php
    │   ├── notification.php
    │   ├── validation.php
    │   └── common.php
    └── en/
        ├── auth.php
        ├── employee.php
        ├── attendance.php
        ├── task.php
        ├── target.php
        ├── kpi.php
        ├── notification.php
        ├── validation.php
        └── common.php
```

- Default locale: `id` (Indonesia)
- Locale switching: `SetLocale` middleware membaca session/cookie `locale`
- Toggle di navbar: tombol ID | EN

### Flutter
```
lib/
└── core/
    └── i18n/
        ├── app_id.arb   ← Bahasa Indonesia
        ├── app_en.arb   ← Bahasa Inggris
        └── l10n.yaml
```

- Menggunakan `flutter_localizations` + `intl`
- Locale disimpan di `SharedPreferences`
- Default: `id`

---

## API Response Envelope

```json
// Success
{
  "success": true,
  "data": { ... },
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 150
  }
}

// Error
{
  "success": false,
  "message": "Anda tidak memiliki akses ke resource ini.",
  "errors": {
    "field": ["pesan error"]
  }
}
```

---

## Multi-Tenant Isolation

1. **Middleware `ResolveTenant`**: resolve tenant dari subdomain atau authenticated user, inject ke request context
2. **Global Scope `TenantScope`**: semua model yang memiliki `tenant_id` otomatis di-scope
3. **Policy layer**: setiap action diverifikasi tenant ownership
4. **Tidak pernah** menerima `tenant_id` dari request body untuk keputusan bisnis

```php
// Contoh Global Scope
class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('tenant_id', tenant()->id);
    }
}
```

---

## Entitlement Service

```php
interface EntitlementServiceInterface
{
    public function canUseFeature(string $feature): bool;
    public function remaining(string $feature): int|null;
    public function limit(string $feature): int|null;
    public function consume(string $feature, int $amount = 1): void;
    public function getUsage(string $feature): int;
}
```

Plan limits disimpan di database, bukan di config/code.

---

## FingerprintProvider Contract

```php
interface FingerprintProvider
{
    public function testConnection(): ProviderResult;
    public function syncEmployees(): ProviderResult;
    public function fetchAttendance(Carbon $from, Carbon $to): ProviderResult;
    public function enrollEmployee(array $data): ProviderResult;
}
```

Implementasi X Solutions:
```
app/Integrations/Fingerprint/XSolutions/XSolutionsProvider.php
```

---

## Queue Jobs

| Job | Queue | Trigger |
|---|---|---|
| SendFcmNotificationJob | notifications | Event terjadi |
| SyncFingerprintJob | fingerprint | Manual / Scheduler |
| GenerateReportJob | reports | User request ekspor |
| CheckOverdueTasksJob | default | Scheduler (tiap jam) |
| CheckTargetRiskJob | default | Scheduler (tiap 6 jam) |
| CheckContractExpiryJob | default | Scheduler (harian) |
| ProcessAttendanceEventJob | attendance | Sync fingerprint |

---

## Halaman Web Admin

| # | Halaman | Route | Akses |
|---|---|---|---|
| 1 | Login | /login | Public |
| 2 | Lupa Password | /forgot-password | Public |
| 3 | Dashboard | /dashboard | All |
| 4 | Karyawan | /employees | HR, Owner |
| 5 | Detail Karyawan | /employees/{id} | HR, Owner |
| 6 | Organisasi | /organization | Owner, HR |
| 7 | Cabang | /branches | Owner, HR |
| 8 | Departemen | /departments | Owner, HR |
| 9 | Tim | /teams | Manager |
| 10 | Jabatan | /positions | HR |
| 11 | Absensi Dashboard | /attendance | HR, Manager |
| 12 | Kalender Absensi | /attendance/calendar | HR, Manager |
| 13 | Detail Absensi | /attendance/{id} | HR, Manager |
| 14 | Fingerprint Devices | /fingerprint/devices | HR |
| 15 | Fingerprint Sync Logs | /fingerprint/sync-logs | HR |
| 16 | Jadwal Kerja | /work-schedules | HR |
| 17 | Shift | /work-shifts | HR |
| 18 | Izin/Cuti | /leaves | HR, Manager |
| 19 | Daftar Tugas | /tasks | All |
| 20 | Task Kanban | /tasks/kanban | Manager, Supervisor |
| 21 | Detail Tugas | /tasks/{id} | All |
| 22 | Proyek | /projects | Manager |
| 23 | Detail Proyek | /projects/{id} | Manager |
| 24 | Target | /targets | Manager, Owner |
| 25 | Detail Target | /targets/{id} | Manager |
| 26 | KPI | /kpi | HR, Manager |
| 27 | Template KPI | /kpi/templates | HR |
| 28 | Performance | /performance | Manager, Owner |
| 29 | Customer | /customers | Manager, Sales |
| 30 | Detail Customer | /customers/{id} | Manager |
| 31 | Field Jobs | /field-jobs | Supervisor, Manager |
| 32 | Notifikasi | /notifications | All |
| 33 | Laporan | /reports | Manager, Owner, HR |
| 34 | Audit Logs | /audit-logs | Owner, Super Admin |
| 35 | Pengaturan | /settings | Owner |
| 36 | Subscription | /subscription | Owner |
| 37 | Usage | /usage | Owner |
| 38 | Manajemen User | /users | Owner, HR |
| 39 | Role | /roles | Owner |
| 40 | Permission | /permissions | Owner |

---

## Screen Flutter

| # | Screen | Route |
|---|---|---|
| 1 | Splash | / |
| 2 | Login | /login |
| 3 | Lupa Password | /forgot-password |
| 4 | Home/Dashboard | /home |
| 5 | Absensi | /attendance |
| 6 | Daftar Tugas | /tasks |
| 7 | Detail Tugas | /tasks/:id |
| 8 | Checklist | /tasks/:id/checklist |
| 9 | Upload Evidence | /tasks/:id/evidence |
| 10 | Target | /targets |
| 11 | Detail Target | /targets/:id |
| 12 | Notifikasi | /notifications |
| 13 | Profil | /profile |
| 14 | Pengaturan | /settings |
| 15 | Jadwal | /schedule |
| 16 | Customer | /customers |
| 17 | Field Job | /field-jobs/:id |
| 18 | Offline Queue | (background indicator) |

---

## Warna & Brand

```
Primary:    #1E3A8A  (Deep Navy Blue)
Secondary:  #2563EB  (Blue)
Accent:     #F59E0B  (Orange/Amber - dari logo)
Success:    #10B981  (Green)
Warning:    #F59E0B  (Amber)
Critical:   #EF4444  (Red)
Neutral:    #6B7280  (Gray)
Background: #F8FAFC  (Light Gray/White)
Surface:    #FFFFFF  (White)
```

Font: **Inter** (Google Fonts)

---

## Komponen UI Reusable

Web (Blade/Alpine.js):
- `x-button` (primary, secondary, danger, ghost)
- `x-input`, `x-select`, `x-date-picker`, `x-search`
- `x-data-table` (dengan pagination, sort, filter)
- `x-stat-card`
- `x-badge` (status badges dengan warna)
- `x-modal`, `x-drawer`
- `x-toast`
- `x-skeleton`
- `x-empty-state`
- `x-confirm-dialog`
- `x-language-toggle` (ID/EN switcher)

Flutter:
- `RmihButton`, `RmihTextField`, `RmihSelect`
- `RmihStatCard`, `RmihBadge`
- `RmihBottomNav`
- `RmihAttendanceButton` (check-in/out besar di home)
- `RmihNotificationBell`
- `RmihLanguageToggle`
- `RmihOfflineIndicator`

---

## Struktur Proyek (Root)

```
rmih/                         ← Laravel project root
├── app/                      ← (struktur modular di atas)
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/                ← Blade templates
│   └── lang/
│       ├── id/               ← Bahasa Indonesia
│       └── en/               ← Bahasa Inggris
├── routes/
│   ├── api.php               ← API routes
│   └── web.php               ← Web routes
├── storage/
│   └── app/
│       └── firebase/         ← FCM service account JSON
└── rmih_flutter/             ← Flutter project (subfolder)
    └── lib/
        └── (struktur flutter di atas)
```
