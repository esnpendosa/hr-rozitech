# Implementation Plan: RMIH Platform

## Overview

Implementasi penuh platform RMIH — SaaS multi-tenant untuk manajemen SDM, absensi, tugas, target, KPI, dan performa. Stack: Laravel 12 + PostgreSQL + Redis + Flutter. Push notification via FCM (gratis). Bilingual Indonesia/Inggris di web admin dan Flutter mobile.

## Task Dependency Graph

```json
{
  "waves": [
    { "wave": 1, "tasks": ["1"] },
    { "wave": 2, "tasks": ["2", "8"] },
    { "wave": 3, "tasks": ["3", "7"] },
    { "wave": 4, "tasks": ["4", "5", "6"] },
    { "wave": 5, "tasks": ["9", "59"] },
    { "wave": 6, "tasks": ["10", "11", "12", "60"] },
    { "wave": 7, "tasks": ["13", "55"] },
    { "wave": 8, "tasks": ["14", "61", "62"] },
    { "wave": 9, "tasks": ["15"] },
    { "wave": 10, "tasks": ["16", "17"] },
    { "wave": 11, "tasks": ["18"] },
    { "wave": 12, "tasks": ["19", "20", "22", "41", "46"] },
    { "wave": 13, "tasks": ["21", "23", "26", "42"] },
    { "wave": 14, "tasks": ["24", "27", "43"] },
    { "wave": 15, "tasks": ["25", "33", "39"] },
    { "wave": 16, "tasks": ["28", "34", "35", "36", "40"] },
    { "wave": 17, "tasks": ["29", "37", "38", "44", "47"] },
    { "wave": 18, "tasks": ["30", "45", "48"] },
    { "wave": 19, "tasks": ["31", "49"] },
    { "wave": 20, "tasks": ["32", "50", "52"] },
    { "wave": 21, "tasks": ["51", "53"] },
    { "wave": 22, "tasks": ["54", "56"] },
    { "wave": 23, "tasks": ["57", "63"] },
    { "wave": 24, "tasks": ["58", "64"] },
    { "wave": 25, "tasks": ["65"] },
    { "wave": 26, "tasks": ["66", "67", "68", "69", "70"] },
    { "wave": 27, "tasks": ["71", "72", "73", "74"] },
    { "wave": 28, "tasks": ["75"] },
    { "wave": 29, "tasks": ["76"] },
    { "wave": 30, "tasks": ["77"] }
  ]
}
```

## Tasks

### Phase 1: Project Foundation & Infrastructure

- [x] 1. Setup Laravel 12 project & core dependencies
  - Initialize Laravel 12 project in `c:\laragon\www\rmih`
  - Install dependencies: sanctum, spatie/laravel-permission, spatie/laravel-auditing, predis, kreait/firebase-php, maatwebsite/excel, barryvdh/laravel-dompdf, intervention/image
  - Configure .env for PostgreSQL, Redis, FCM, locale
  - Set APP_LOCALE=id, APP_FALLBACK_LOCALE=en
  - Setup modular monolith directory structure: app/Domain/, app/Application/, app/Integrations/, app/Http/
  - Configure Laravel Sanctum for API authentication
  - Configure Spatie Permission
  - Configure Redis as cache and queue driver
  - Setup storage symlink and filesystem config
  - _Requirements: 1.1_

- [x] 2. Database migrations — Core tables
  - Create migration: tenants, tenant_settings, tenant_domains
  - Create migration: subscription_plans, plan_features, subscriptions, subscription_items, usage_counters
  - Create migration: users (with fcm_token, locale fields)
  - Create migration: user_sessions (device tracking)
  - Create migration: roles, permissions, role_permissions, user_roles (via spatie/laravel-permission)
  - All PKs use UUID, all tenant-owned tables have tenant_id FK
  - Add composite indexes per design.md indexing strategy
  - _Requirements: 1.2, 1.3_
  - _Depends on: 1_

- [x] 3. Database migrations — Organization & Employee tables
  - Create migration: companies, branches, departments, divisions, teams, positions, work_locations
  - Create migration: employees, employee_contracts, employee_documents, employee_emergency_contacts, employee_histories
  - Add all composite indexes
  - _Requirements: 1.2, 1.3_
  - _Depends on: 2_

- [x] 4. Database migrations — Attendance & Fingerprint tables
  - Create migration: work_shifts, work_schedules
  - Create migration: attendance_records, attendance_events (with UNIQUE constraint on tenant_id+employee_id+attendance_date+source_event_id), attendance_corrections
  - Create migration: leave_types, leave_requests, overtime_requests
  - Create migration: fingerprint_devices, fingerprint_device_users, fingerprint_sync_runs, fingerprint_sync_errors, fingerprint_mappings
  - Add all composite indexes
  - _Requirements: 1.2, 1.3_
  - _Depends on: 3_

- [x] 5. Database migrations — Task, Project, Target, KPI, Performance tables
  - Create migration: projects, project_members
  - Create migration: tasks, task_assignees, task_checklists, task_comments, task_attachments, task_evidences, task_status_histories
  - Create migration: targets, target_assignments, target_progress, target_periods
  - Create migration: kpi_templates, kpi_metrics, kpi_assignments, kpi_results
  - Create migration: performance_periods, performance_results (with versioning support)
  - Add all composite indexes
  - _Requirements: 1.2, 1.3_
  - _Depends on: 3_

- [x] 6. Database migrations — Customer, Notification, File, Audit tables
  - Create migration: customers, customer_contacts, customer_locations, field_jobs
  - Create migration: notifications, notification_preferences, notification_logs
  - Create migration: files (private file registry)
  - Create migration: audit_logs, activity_logs, system_events
  - Add all composite indexes
  - _Requirements: 1.2, 1.3_
  - _Depends on: 3_

- [x] 7. Multi-tenant infrastructure
  - Create TenantScope (Global Scope) applied automatically to all tenant-owned models
  - Create ResolveTenant middleware (resolves tenant from authenticated user context, NOT from request body)
  - Create CheckEntitlement middleware
  - Create SetLocale middleware (reads locale from session/cookie/user preference, defaults to 'id')
  - Register middlewares in bootstrap/app.php
  - Ensure TenantScope is applied to all Domain models with tenant_id
  - _Requirements: 1.4_
  - _Depends on: 2_

- [x] 8. Queue & Cache configuration
  - Configure Redis queue driver with named queues: default, notifications, fingerprint, reports, attendance
  - Create SendFcmNotificationJob skeleton
  - Create SyncFingerprintJob skeleton
  - Create GenerateReportJob skeleton
  - Create CheckOverdueTasksJob scheduled command
  - Create CheckTargetRiskJob scheduled command
  - Create CheckContractExpiryJob scheduled command
  - Register scheduled jobs in Console/Kernel.php or routes/console.php
  - _Requirements: 1.5_
  - _Depends on: 1_

### Phase 2: Authentication & Access Control

- [x] 9. Authentication API (Login, Logout, Refresh, Me)
  - Create AuthController with: login, logout, refresh, me
  - POST /api/v1/auth/login — validate credentials, create Sanctum token, record user_session, audit log
  - POST /api/v1/auth/logout — revoke token, audit log
  - POST /api/v1/auth/refresh — issue new token
  - GET /api/v1/auth/me — return authenticated user with roles/permissions
  - Rate limiting on login endpoint (5 attempts / 5 min throttle)
  - POST /api/v1/auth/fcm-token — register FCM token
  - _Requirements: 2.1, 2.6_
  - _Depends on: 2, 7, 8_

- [x] 10. Password reset flow
  - POST /api/v1/auth/forgot-password — send reset email
  - POST /api/v1/auth/reset-password — validate token, update password
  - Reset link expires in 60 minutes
  - Email template in ID and EN
  - _Requirements: 2.2_
  - _Depends on: 9_

- [x] 11. RBAC — Roles, Permissions, Policies
  - Define all platform roles: super_admin, owner, hr_admin, manager, supervisor, employee, field_worker
  - Create database seeder for default permissions per role
  - Create Policies for each major resource (EmployeePolicy, TaskPolicy, TargetPolicy, AttendancePolicy, etc.)
  - All policies enforce: tenant_id match + role permission + ownership
  - Create RoleController and PermissionController for web admin
  - _Requirements: 2.4_
  - _Depends on: 9_

- [x] 12. Session management
  - GET /api/v1/auth/sessions — list active sessions for current user
  - DELETE /api/v1/auth/sessions/{id} — revoke a specific session
  - Sessions stored in user_sessions with: device_name, ip, user_agent, last_active_at
  - _Requirements: 2.5_
  - _Depends on: 9_

### Phase 3: SaaS Multi-Tenant & Subscription

- [x] 13. Tenant registration & management
  - POST /api/v1/platform/tenants — create new tenant (Super Admin)
  - GET /api/v1/platform/tenants — list all tenants (Super Admin)
  - PUT /api/v1/platform/tenants/{id} — update tenant status
  - Tenant registration form (web): creates tenant + owner user + activates STARTER plan
  - Super Admin can view all tenants and suspend/activate
  - _Requirements: 3.1, 3.2_
  - _Depends on: 9, 11_

- [x] 14. Subscription plans & entitlement
  - Create SubscriptionPlan seeder: STARTER (10 emp), BUSINESS (30 emp), PROFESSIONAL (75 emp), ENTERPRISE (custom)
  - Implement EntitlementService with: canUseFeature(), remaining(), limit(), consume(), getUsage()
  - Plan limits stored in plan_features table, NOT hardcoded
  - Usage tracked in usage_counters
  - CheckEntitlement middleware blocks feature access based on plan
  - _Requirements: 3.3, 3.4, 3.5_
  - _Depends on: 13_

### Phase 4: Organization Management

- [x] 15. Organization API & Web — Company & Branch
  - CRUD for Company (GET/POST/PUT/DELETE /api/v1/organization/company)
  - CRUD for Branches (/api/v1/branches)
  - Web pages: Organization overview, Branch list, Branch form
  - All changes audited
  - Bilingual labels in all views
  - _Requirements: 4.1, 4.2_
  - _Depends on: 13_

- [x] 16. Organization API & Web — Department, Division, Team, Position
  - CRUD for Departments, Divisions, Teams, Positions
  - Hierarchy display: Company → Division → Department → Team → Position
  - Position linked to employees
  - Web pages: Department, Team, Position management
  - Bilingual labels
  - _Requirements: 4.3, 4.4_
  - _Depends on: 15_

- [x] 17. Work Locations
  - CRUD for WorkLocations with GPS coordinates and geofence radius
  - GET/POST/PUT/DELETE /api/v1/work-locations
  - Used by attendance geofence validation
  - Web page: Work Location list and form
  - _Requirements: 4.5_
  - _Depends on: 15_

### Phase 5: Employee Management

- [x] 18. Employee API — CRUD
  - GET /api/v1/employees (with pagination, search, filters: name, NIK, branch, department, status)
  - POST /api/v1/employees — create with auto-generated employee_number
  - GET /api/v1/employees/{id}
  - PUT /api/v1/employees/{id}
  - DELETE /api/v1/employees/{id} (soft delete)
  - All changes record to employee_histories and audit_logs
  - Entitlement check on employee count before creation
  - _Requirements: 5.1, 5.5_
  - _Depends on: 14, 16_

- [x] 19. Employee — Contract & Documents
  - POST /api/v1/employees/{id}/contracts — add employment contract
  - GET/POST /api/v1/employees/{id}/documents — upload (validate MIME, size, store private)
  - GET /api/v1/employees/{id}/documents/{docId}/download — signed URL
  - CheckContractExpiryJob sends notification 30/7/1 day before expiry
  - _Requirements: 5.2, 5.3_
  - _Depends on: 18_

- [x] 20. Employee — Emergency Contacts & History
  - CRUD for emergency contacts
  - GET /api/v1/employees/{id}/history — employee change history
  - _Requirements: 5.4, 5.5_
  - _Depends on: 18_

- [x] 21. Employee Web Pages
  - Employee List page: data table with search, filter, export
  - Employee Detail page: profile, contract, documents, history tabs
  - Employee Create/Edit form: all required fields with validation
  - All labels/messages bilingual ID/EN
  - _Requirements: 5.1-5.6_
  - _Depends on: 18, 19, 20_

### Phase 6: Attendance System

- [x] 22. Work Shifts & Schedules API
  - CRUD for WorkShifts: name, start_time, end_time, tolerance_minutes, overtime_threshold
  - CRUD for WorkSchedules: assign shifts to employee/team with recurrence rules
  - GET/POST/PUT/DELETE /api/v1/work-shifts
  - GET/POST /api/v1/work-schedules
  - _Requirements: 6.2_
  - _Depends on: 18_

- [x] 23. Check-in & Check-out API
  - POST /api/v1/attendance/check-in: record time, GPS, photo (optional), device_id
  - POST /api/v1/attendance/check-out: record time, GPS
  - Validate geofence if work_location has geofence configured
  - Detect late arrival based on shift schedule
  - _Requirements: 6.1_
  - _Depends on: 17, 22_

- [x] 24. Attendance records & calendar API
  - GET /api/v1/attendance — list with filters
  - GET /api/v1/attendance/today — today's summary
  - GET /api/v1/attendance/calendar — monthly calendar view data
  - GET /api/v1/attendance/{id}
  - _Requirements: 6.3_
  - _Depends on: 23_

- [x] 25. Attendance correction workflow
  - POST /api/v1/attendance/corrections — employee submits correction
  - GET /api/v1/attendance/corrections — manager/HR views
  - PUT /api/v1/attendance/corrections/{id}/approve and /reject
  - Notification sent to approver on submission, to employee on decision
  - Audit log on every status change
  - _Requirements: 6.4_
  - _Depends on: 24_

- [x] 26. Leave & Permission management
  - CRUD for leave types: /api/v1/leave-types
  - POST /api/v1/leaves — employee submits request
  - PUT /api/v1/leaves/{id}/approve and /reject
  - Leave balance auto-deducted on approval
  - Notification to approver and employee
  - _Requirements: 6.5_
  - _Depends on: 18, 9_

- [x] 27. Overtime management
  - POST /api/v1/overtime — submit overtime record
  - GET /api/v1/overtime
  - PUT /api/v1/overtime/{id}/approve and /reject
  - _Requirements: 6.6_
  - _Depends on: 22_

- [x] 28. Attendance web pages
  - Attendance Dashboard: today's stats (present/absent/late/on leave)
  - Attendance Calendar: monthly view per employee
  - Attendance Detail page
  - Leave management page with approval actions
  - Work Shifts and Schedules pages
  - Bilingual ID/EN
  - _Requirements: 6.7, 6.1-6.6_
  - _Depends on: 22, 23, 24, 25, 26, 27_

### Phase 7: Fingerprint Integration

- [x] 29. Fingerprint Provider contract & X Solutions adapter
  - Create `app/Integrations/Fingerprint/Contracts/FingerprintProvider.php` interface
  - Create `app/Integrations/Fingerprint/XSolutions/XSolutionsProvider.php` implementing interface
  - Methods: testConnection(), syncEmployees(), fetchAttendance(), enrollEmployee()
  - Bind interface to X Solutions implementation in AppServiceProvider
  - _Requirements: 7.2_
  - _Depends on: 22, 28_

- [x] 30. Fingerprint device management API
  - POST /api/v1/fingerprint/devices — register device
  - GET /api/v1/fingerprint/devices — list devices with status
  - POST /api/v1/fingerprint/devices/{id}/test — test connection
  - POST /api/v1/fingerprint/devices/{id}/employees — map employee to device
  - GET /api/v1/fingerprint/devices/{id}/sync-logs
  - _Requirements: 7.1, 7.3_
  - _Depends on: 29_

- [x] 31. Fingerprint sync service & job
  - Implement FingerprintSyncService: pull attendance, process events, protect duplicates
  - SyncFingerprintJob dispatched to 'fingerprint' queue
  - Idempotent sync using UNIQUE constraint (source_event_id)
  - Retry with exponential backoff on failure
  - Record errors in fingerprint_sync_errors
  - POST /api/v1/fingerprint/devices/{id}/sync — trigger manual sync
  - Scheduled automatic sync every 15 minutes
  - _Requirements: 7.4, 7.5_
  - _Depends on: 29, 30_

- [x] 32. Fingerprint web pages
  - Fingerprint Devices list page with status indicator
  - Device detail page: config, employee mapping, test connection button
  - Sync Logs page: run history, error details
  - Manual sync trigger button
  - Bilingual ID/EN
  - _Requirements: 7.6_
  - _Depends on: 30, 31_

### Phase 8: Task Management

- [x] 33. Task CRUD API
  - GET /api/v1/tasks (paginated, filters: status, priority, assignee, project, due_at range)
  - POST /api/v1/tasks — create, notify assignees via FCM
  - GET /api/v1/tasks/{id}
  - PUT /api/v1/tasks/{id}
  - DELETE /api/v1/tasks/{id} (soft delete)
  - POST /api/v1/tasks/{id}/status — update status with lifecycle validation
  - Record status history in task_status_histories
  - _Requirements: 8.1, 8.2_
  - _Depends on: 18, 22, 28_

- [x] 34. Task checklist & comments API
  - GET/POST/PUT/DELETE /api/v1/tasks/{id}/checklists
  - POST checklist item completion: update progress_percent
  - GET/POST /api/v1/tasks/{id}/comments
  - @mention in comments triggers notification
  - _Requirements: 8.3, 8.5_
  - _Depends on: 33_

- [x] 35. Task evidence upload API
  - POST /api/v1/tasks/{id}/evidence — upload file
  - Validate MIME (image/jpeg, image/png, application/pdf, video/mp4), max 10MB
  - Store in private storage, record GPS coordinates if provided
  - GET /api/v1/tasks/{id}/evidence — list with signed URLs
  - _Requirements: 8.4_
  - _Depends on: 33_

- [x] 36. Task automation — Overdue & notifications
  - CheckOverdueTasksJob: runs hourly, updates tasks past due_at to 'overdue', sends notification
  - Deadline approaching notification: 1 day and 3 hours before due_at
  - All notifications dispatched to 'notifications' queue
  - _Requirements: 8.7_
  - _Depends on: 33, 9_

- [x] 37. Task web pages
  - Task list page: data table with filters, search, bulk actions
  - Task Kanban page: columns per status
  - Task detail page: full detail, checklist, evidence, comments, status history
  - Task create/edit form
  - Bilingual ID/EN
  - _Requirements: 8.1-8.8_
  - _Depends on: 33, 34, 35_

### Phase 9: Project Management

- [x] 38. Project API & Web
  - CRUD for Projects: GET/POST/PUT/DELETE /api/v1/projects
  - Project member management: POST /api/v1/projects/{id}/members, DELETE member
  - GET /api/v1/projects/{id}/tasks — tasks in project
  - Progress aggregation via background job (not real-time recalc)
  - Web pages: Project list, Project detail (tasks, members, progress chart)
  - Bilingual ID/EN
  - _Requirements: 9.1-9.4_
  - _Depends on: 33_

### Phase 10: Target Management

- [x] 39. Target API
  - CRUD for Targets: GET/POST/PUT/DELETE /api/v1/targets
  - POST /api/v1/targets/{id}/progress — update actual value
  - Auto-calculate: progress_percent, remaining_value, required_daily_rate
  - Risk assessment (configurable): ON_TRACK / AT_RISK / CRITICAL / COMPLETED / EXPIRED
  - CheckTargetRiskJob: runs every 6 hours, sends notification for AT_RISK/CRITICAL
  - GET /api/v1/targets/{id} includes calculation breakdown
  - _Requirements: 10.1-10.3_
  - _Depends on: 18, 22, 28_

- [x] 40. Target web pages
  - Target list page: table with risk level badges, period filter
  - Target detail page: calculation breakdown, progress history chart
  - Target create/edit form
  - Bilingual ID/EN
  - _Requirements: 10.4, 10.5_
  - _Depends on: 39_

### Phase 11: KPI Engine

- [x] 41. KPI Templates & Assignment API
  - CRUD for KpiTemplates: GET/POST/PUT/DELETE /api/v1/kpi/templates
  - CRUD for KpiMetrics within a template (validate total weight = 100%)
  - POST /api/v1/kpi/assignments — assign template to employee for a period
  - GET /api/v1/kpi/assignments
  - _Requirements: 11.1, 11.2_
  - _Depends on: 18_

- [x] 42. KPI input & scoring API
  - POST /api/v1/kpi/assignments/{id}/actuals — input actual values
  - Auto-calculate KPI score based on configurable scoring method
  - Store results in kpi_results with period versioning
  - Results immutable once period is closed
  - _Requirements: 11.3, 11.4_
  - _Depends on: 41_

- [x] 43. KPI web pages
  - KPI list page: employee KPI overview with scores
  - KPI Template page: template builder with metric weights
  - KPI detail: per-metric breakdown
  - Bilingual ID/EN
  - _Requirements: 11.1-11.4_
  - _Depends on: 41, 42_

### Phase 12: Performance Engine

- [x] 44. Performance configuration & calculation API
  - POST /api/v1/performance/config — save performance weight configuration
  - Config stored with versioning
  - POST /api/v1/performance/calculate — calculate performance for a period
  - Stores results in performance_results (append, no retroactive change)
  - GET /api/v1/performance — list results with filters
  - _Requirements: 12.1, 12.2_
  - _Depends on: 41, 42, 39_

- [x] 45. Performance web pages
  - Performance dashboard: top/bottom performers, trend chart, department comparison
  - Individual performance detail page
  - Configuration page for weight settings
  - Bilingual ID/EN
  - _Requirements: 12.3_
  - _Depends on: 44_

### Phase 13: Customer & Field Work

- [x] 46. Customer management API & Web
  - CRUD for Customers: GET/POST/PUT/DELETE /api/v1/customers
  - GET /api/v1/customers/{id}/jobs — field job history
  - Web pages: Customer list, Customer detail
  - Bilingual ID/EN
  - _Requirements: 13.1_
  - _Depends on: 18_

- [x] 47. Field Job API & Web
  - POST /api/v1/field-jobs — create job
  - GET /api/v1/field-jobs, GET /api/v1/field-jobs/{id}
  - PUT /api/v1/field-jobs/{id}/checkin — GPS check-in
  - POST /api/v1/field-jobs/{id}/checklist, /evidence, /complete
  - Web pages: Field Job list, Field Job detail
  - Bilingual ID/EN
  - _Requirements: 13.2, 13.3_
  - _Depends on: 46_

### Phase 14: Notification System (FCM)

- [x] 48. FCM setup & notification infrastructure
  - Install kreait/firebase-php
  - Store Firebase service account JSON at storage/app/firebase/service-account.json
  - Create FcmService: send single notification, send to multiple tokens
  - Create NotificationService: createNotification() saves to DB + dispatches FCM job
  - SendFcmNotificationJob: async FCM send via 'notifications' queue with retry
  - FCM token registration endpoint already in task 9
  - _Requirements: 14.1, 14.3_
  - _Depends on: 9, 33, 39, 8_

- [x] 49. Notification API endpoints
  - GET /api/v1/notifications — paginated list, unread first
  - POST /api/v1/notifications/{id}/read — mark as read
  - POST /api/v1/notifications/read-all — mark all as read
  - GET /api/v1/notifications/count — unread count
  - GET /api/v1/notification-preferences
  - PUT /api/v1/notification-preferences — update per-type toggle
  - _Requirements: 14.2, 14.5, 14.6_
  - _Depends on: 48_

- [x] 50. Notification triggers integration
  - Wire all notification events: task assigned, deadline approaching, task overdue, target at_risk/critical, attendance issue, leave submitted/approved/rejected, correction submitted/decided
  - All notifications use NotificationService (not direct FCM calls)
  - Notification content in ID and EN based on user locale
  - _Requirements: 14.4_
  - _Depends on: 48, 49_

- [x] 51. Notification web page
  - Notification center page: list with read/unread state, type badge, timestamp
  - Mark read action
  - Notification preferences page: toggle per type
  - Bell icon in navbar with unread count badge
  - Bilingual ID/EN
  - _Requirements: 14.2, 14.5, 14.6_
  - _Depends on: 49_

### Phase 15: Reports & Export

- [x] 52. Reports API
  - GET /api/v1/reports/attendance — with filters, returns aggregated data
  - GET /api/v1/reports/tasks — completion rate, overdue%, distribution
  - GET /api/v1/reports/targets — achievement%, risk distribution
  - GET /api/v1/reports/performance — KPI scores, rankings
  - POST /api/v1/reports/{type}/export — triggers async job
  - GET /api/v1/reports/exports/{id}/download — download generated file
  - _Requirements: 15.1-15.4_
  - _Depends on: 22, 33, 39, 44_

- [x] 53. Async report generation
  - GenerateReportJob: generate CSV/XLSX/PDF using maatwebsite/excel and dompdf
  - Store generated file in private storage with 7-day TTL
  - Notify user via FCM + in-app when file is ready
  - Auto-delete expired files via scheduler
  - _Requirements: 15.5_
  - _Depends on: 52, 48_

- [x] 54. Reports web pages
  - Reports hub page: links to each report type
  - Attendance report page with chart + table + export button
  - Task report page
  - Target report page
  - Performance report page
  - Export status indicator (queued/generating/ready/download)
  - Bilingual ID/EN
  - _Requirements: 15.1-15.5_
  - _Depends on: 52, 53_

### Phase 16: Audit Logs

- [x] 55. Audit log system
  - Configure spatie/laravel-auditing on all sensitive models
  - Audit record includes: tenant_id, actor_user_id, action, entity_type, entity_id, old_values, new_values, ip, user_agent, request_id, created_at
  - Append-only (no update/delete permissions for normal roles)
  - Web page: Audit Logs table with filters (actor, action, entity, date range, IP)
  - Only Owner and Super Admin can access
  - Bilingual ID/EN
  - _Requirements: 16.1-16.3_
  - _Depends on: 7_

### Phase 17: Dashboard Pages

- [x] 56. Owner/Admin Dashboard
  - Stat cards: employee count, present today, absent, active tasks, overdue tasks, target achievement%, avg KPI
  - Charts: attendance trend (7/30 days), task trend, target trend
  - Alerts section: overdue tasks, AT_RISK targets, pending approvals count
  - Data via dedicated cached dashboard API endpoints
  - Bilingual ID/EN
  - _Requirements: 17.1_
  - _Depends on: 22, 33, 39, 44, 48_

- [x] 57. Manager Dashboard
  - Team overview: attendance today, active tasks, target status
  - Pending reviews section
  - My team members with status indicators
  - Bilingual ID/EN
  - _Requirements: 17.2_
  - _Depends on: 56_

### Phase 18: Settings & User Management

- [x] 58. Settings & User Management pages
  - Company settings: logo, name, address, timezone, locale default
  - Notification settings (tenant-level defaults)
  - Subscription & Usage page: current plan, usage bars, upgrade CTA
  - User Management page: list users, create/deactivate, assign role
  - Roles and Permissions pages
  - Bilingual ID/EN
  - _Requirements: various_
  - _Depends on: 13, 14_

### Phase 19: Bilingual System (ID/EN)

- [x] 59. Web i18n — translation files
  - Create resources/lang/id/ and resources/lang/en/ directories
  - Create all translation files: auth.php, common.php, employee.php, attendance.php, task.php, target.php, kpi.php, performance.php, notification.php, validation.php, report.php, organization.php, customer.php, fingerprint.php, dashboard.php
  - All UI text uses __() or @lang() — no hardcoded strings
  - _Requirements: 19.1, 19.3_
  - _Depends on: 1_

- [x] 60. Web i18n — locale switching
  - SetLocale middleware: reads locale from session/cookie/user.locale preference
  - Language toggle component in navbar: ID | EN buttons
  - POST /locale — switch locale, save to session + user.locale
  - Default locale: id (Indonesia)
  - _Requirements: 19.1_
  - _Depends on: 59_

### Phase 20: Landing Page

- [x] 61. Landing page — all sections
  - Hero section: bilingual headline/subheadline/CTA
  - Features section: 10 feature cards with icons, bilingual
  - Product preview: dashboard screenshot mockup
  - How it works flow: People → Attendance → Work → Target → Performance
  - Mobile app preview section
  - Security highlights section
  - Pricing section: 4 plans (STARTER, BUSINESS, PROFESSIONAL, ENTERPRISE) bilingual
  - Trust section: company logos
  - Final CTA section and footer
  - Language toggle (ID/EN) in navbar
  - Responsive: mobile + tablet + desktop
  - _Requirements: 18.1-18.3_
  - _Depends on: 59, 60_

- [x] 62. Auth pages — Login & Forgot Password web
  - Login page: email/password form, forgot password link, RMIH branding
  - Forgot password page: email form, success message
  - Both pages bilingual (ID/EN)
  - Responsive design with brand colors
  - _Requirements: 2.1, 2.2_
  - _Depends on: 59, 60_

### Phase 21: Flutter Mobile App

- [x] 63. Flutter project setup
  - Initialize Flutter project in rmih_flutter/ subfolder
  - Add dependencies: dio, flutter_secure_storage, firebase_messaging, firebase_core, flutter_localizations, intl, shared_preferences, geolocator, image_picker, cached_network_image, fl_chart, go_router
  - Create feature-oriented structure: lib/core/, lib/features/, lib/shared/
  - Setup i18n: create lib/core/i18n/app_id.arb and app_en.arb with all strings
  - Setup RmihTheme with brand colors (navy, blue, orange accent) and Inter font
  - Setup Dio HTTP client with auth interceptor (Bearer token), base URL config
  - _Requirements: 21.1, 19.2_
  - _Depends on: 9, 48_

- [x] 64. Flutter — Auth screens
  - Splash screen: RMIH logo, auto-navigate to login or home based on stored token
  - Login screen: email/password, forgot password link, bilingual
  - Forgot password screen: email field, success state
  - Token stored in flutter_secure_storage
  - _Requirements: 21.2_
  - _Depends on: 63_

- [x] 65. Flutter — Home/Dashboard screen
  - Greeting with employee name
  - Large check-in / check-out button (prominent, contextual state)
  - Today's attendance status indicator
  - My tasks summary (due today, overdue count)
  - My target summary (progress %)
  - Notifications bell with unread count
  - Bottom navigation: Home, Tasks, Target, Notifications, Profile
  - Bilingual ID/EN
  - _Requirements: 21.2, 17.3_
  - _Depends on: 64_

- [x] 66. Flutter — Attendance screens
  - Check-in/out screen with GPS detection, selfie photo (optional)
  - Geofence validation feedback
  - Attendance history: calendar + list view
  - Leave request form
  - Schedule viewer
  - Bilingual ID/EN
  - _Requirements: 21.2, 6.1_
  - _Depends on: 63, 23, 24_

- [x] 67. Flutter — Task screens
  - Task list: filterable by status, priority, due date
  - Task detail: info, checklist (tap to complete), comments, evidence list
  - Add comment
  - Upload evidence: camera/gallery picker
  - Status update actions
  - Bilingual ID/EN
  - _Requirements: 21.2, 8.1-8.8_
  - _Depends on: 63, 33, 34, 35_

- [x] 68. Flutter — Target screens
  - Target list with progress bars and risk badges
  - Target detail: achievement%, remaining, daily rate, progress history
  - Update progress input
  - Bilingual ID/EN
  - _Requirements: 21.2, 10.1-10.4_
  - _Depends on: 63, 39_

- [x] 69. Flutter — Notifications screen
  - Notification list: unread first, type icon, time ago
  - Tap to mark as read and navigate to relevant screen
  - Mark all read button
  - FCM: handle foreground, background, terminated app states
  - Bilingual ID/EN
  - _Requirements: 21.2, 21.3, 14.1-14.6_
  - _Depends on: 63, 49_

- [x] 70. Flutter — Profile & Settings screen
  - Profile: photo, name, position, department, branch
  - Language toggle: ID / EN (saves to SharedPreferences + user API)
  - Notification preferences
  - Logout (revoke token)
  - Bilingual ID/EN
  - _Requirements: 21.2, 19.2_
  - _Depends on: 63, 64_

- [x] 71. Flutter — Customer & Field Job screens
  - Customer list and detail screen
  - Field Job screen: job info, GPS check-in, checklist, evidence upload, notes, signature capture, complete job
  - Offline queue: store job data locally when no internet, sync on reconnect
  - Offline indicator widget
  - Bilingual ID/EN
  - _Requirements: 21.2, 21.4, 13.2-13.4_
  - _Depends on: 63, 46, 47_

### Phase 22: Security & Hardening

- [x] 72. API security hardening
  - Verify all endpoints have authorization (no unprotected routes)
  - Audit for mass assignment vulnerabilities ($fillable in all models)
  - Verify tenant isolation on every controller/service
  - Add rate limiting: auth endpoints (strict), general API (configurable per plan)
  - Input validation on all Form Requests
  - Prevent SSRF on URL-accepting fields
  - File upload: verify actual file signature
  - _Requirements: 20.1-20.4_
  - _Depends on: 18, 22, 33, 39, 44, 46_

- [x] 73. File storage security
  - Ensure all uploaded files stored outside public web root
  - Signed URL generation with 30-minute expiry for private files
  - Safe filename generation (UUID-based)
  - MIME whitelist enforcement
  - Max file size enforcement per type
  - _Requirements: 20.2_
  - _Depends on: 19, 35_

### Phase 23: Seeder & Demo Data

- [x] 74. Database seeders
  - SubscriptionPlanSeeder: STARTER, BUSINESS, PROFESSIONAL, ENTERPRISE with features and limits
  - RolePermissionSeeder: all roles with default permissions
  - DemoTenantSeeder: 1 demo tenant with owner, sample org structure
  - DemoEmployeeSeeder: 10+ sample employees across departments
  - DemoDataSeeder: sample tasks, targets, attendance records, KPI for demo
  - _Requirements: all_
  - _Depends on: 18, 22, 33, 39_

### Phase 24: Final Integration & Polish

- [x] 75. API documentation & consistency
  - Ensure all routes are in routes/api.php with /api/v1/ prefix
  - Consistent response envelope {success, data, meta} on all endpoints
  - JSON 404 and 500 error responses (not HTML)
  - Pagination on all list endpoints
  - _Requirements: all_
  - _Depends on: 72_

- [x] 76. Final UI polish & responsive testing
  - Verify all web pages are responsive (mobile/tablet/desktop)
  - Loading states (skeleton loaders) on all data-fetching pages
  - Empty state components on all list pages
  - Error state handling
  - Consistent brand colors, Inter font, component library
  - Verify all strings are translated (no hardcoded strings)
  - _Requirements: 19.1, 19.3_
  - _Depends on: 59, 60, 75_

- [x] 77. Performance optimization
  - Cache dashboard aggregates with Redis (key: tenant:{id}:dashboard:{period})
  - Cache permission maps per user
  - Cache subscription limits per tenant
  - Eager loading on all N+1-prone queries
  - Verify all high-volume queries use proper indexes
  - _Requirements: 1.5, various_
  - _Depends on: 75, 76_


## Notes

- **Stack**: Laravel 12 + PostgreSQL + Redis + Flutter Android
- **Push notification**: Firebase Cloud Messaging (FCM) — gratis, tidak ada biaya
- **Bilingual**: Indonesia (default) dan Inggris — berlaku di web admin dan Flutter mobile
- **Multi-tenant**: tenant_id di-resolve dari authenticated context, TIDAK dari request body
- **Queue**: semua job mahal (export, sync, FCM) berjalan async melalui Redis queue
- **Performance versioning**: hasil performance tidak berubah retroaktif ketika konfigurasi berubah
- **Fingerprint**: implementasi X Solutions diisolasi di app/Integrations/Fingerprint/XSolutions/
- **File upload**: semua file disimpan di private storage, diakses via signed URL
- **i18n key convention**: snake_case, dikelompokkan per modul (mis. task.status.overdue)
- Proyek Laravel ada di root `c:\laragon\www\rmih`, Flutter di subfolder `rmih_flutter/`
