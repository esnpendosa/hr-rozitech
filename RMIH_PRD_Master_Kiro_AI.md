# RMIH — Resource Management Integrated Human
## Product Requirements Document (PRD) Master
### Edition: SaaS + Web Admin + Flutter Mobile + Fingerprint + AI-Ready Architecture

**Product Name:** RMIH  
**Full Name:** Resource Management Integrated Human  
**Product Type:** B2B SaaS Workforce / Human Resource / Task & Performance Management Platform  
**Primary Stack:** Laravel 12 + PostgreSQL + Redis + Flutter  
**Architecture:** Multi-tenant SaaS, Modular Monolith, API-first, AI-ready  
**Primary Mobile Platform:** Android APK via Flutter  
**Fingerprint Integration:** X Solutions fingerprint device/API/SDK, implemented behind a provider abstraction  
**AI:** Future phase; architecture prepared in V1, actual LLM/RAG/Voice enabled in later phases  
**Document Purpose:** Master execution specification for development with Kiro AI

---

# 1. Executive Summary

RMIH is a modern, secure, scalable, multi-tenant SaaS platform for managing people, attendance, work, tasks, targets, projects, KPI, performance, reports, and operational monitoring in one integrated system.

RMIH is not intended to be only a conventional HR application. The central product concept is:

> **Employee → Attendance → Task → Target → Progress → KPI → Performance → Report → Intelligence**

The system must be designed from day one so that future AI capabilities can access business information through controlled application services/tools rather than unrestricted database access.

The first production version (V1) focuses on:

1. Organization and company management
2. Employee management
3. Role and permission management
4. Attendance
5. Fingerprint integration
6. Work schedules and shifts
7. Leave/permission
8. Task management
9. Project management
10. Target management
11. KPI
12. Performance
13. Customer and field-work support
14. Notifications
15. Reports
16. Audit logs
17. Multi-tenant SaaS
18. Subscription and usage limits
19. Flutter mobile application

Future versions add:

- AI Assistant
- RAG / knowledge base
- AI operational summaries
- AI target-risk monitoring
- AI-generated reports
- AI Agent / controlled actions
- Voice-to-Text
- Text-to-Voice

Voice and LLM features are explicitly **future scope**, not mandatory for V1.

---

# 2. Product Vision

## Vision

Create a workforce platform where management can understand the condition of the organization from one system, while employees have one mobile application for attendance, tasks, targets, reports, and work activity.

## Mission

Reduce fragmented operational management caused by:

- WhatsApp-based task assignments
- Spreadsheet-based targets
- Separate fingerprint attendance systems
- Manual KPI calculations
- Manual employee records
- Delayed progress reports
- Inconsistent field-work evidence
- Lack of centralized audit trails

## Core Principle

RMIH should answer these questions:

- Who is working?
- Who is absent?
- What is everyone working on?
- What is the target?
- How much has been completed?
- Which work is late?
- Which target is at risk?
- Which team needs attention?
- What evidence proves the work?
- How is employee/team performance changing?

---

# 3. Product Positioning

RMIH should be positioned as:

> **Integrated Workforce Management + Task + Target + KPI + Performance SaaS**

Not simply:

> HRIS / payroll software.

The differentiating concept is the connection between people and measurable work outcomes.

---

# 4. Target Customers

## Primary

- Small and medium businesses
- Service companies
- ISP / telecom companies
- Field service companies
- Construction/service teams
- Retail chains
- Agencies
- Software companies
- Logistics companies
- Multi-branch businesses

## Enterprise

- Companies with multiple branches
- Companies with hundreds/thousands of employees
- Organizations requiring strict permissions and audit trails
- Companies requiring dedicated deployment

---

# 5. User Roles

The system must support granular RBAC.

## 5.1 Platform Super Admin

Controls the SaaS platform:

- tenants
- plans
- subscriptions
- platform settings
- feature flags
- system health
- support access
- platform-level audit

Must not automatically expose tenant business data without explicit privileged access/audit.

## 5.2 Company Owner

- company dashboard
- employee overview
- attendance
- tasks
- targets
- KPI
- performance
- reports
- branch overview
- subscription/usage

## 5.3 HR Admin

- employees
- employment data
- documents
- attendance
- shifts
- leave
- reports

## 5.4 Manager

- team
- task assignment
- target
- KPI
- performance
- approvals
- reports

## 5.5 Supervisor

- daily operations
- task monitoring
- task assignment
- checklist
- field evidence
- target monitoring

## 5.6 Employee

- own profile
- attendance
- schedule
- assigned tasks
- target
- checklist
- reports
- notifications

## 5.7 Field Worker / Technician

Employee capabilities plus:

- customer jobs
- GPS/location
- field checklist
- photo evidence
- signature
- work notes
- job completion

---

# 6. SaaS Multi-Tenant Model

RMIH is a multi-tenant SaaS.

Concept:

```text
RMIH Platform
├── Tenant A
│   ├── Branches
│   ├── Departments
│   ├── Employees
│   ├── Tasks
│   └── Attendance
├── Tenant B
│   ├── Branches
│   ├── Departments
│   ├── Employees
│   ├── Tasks
│   └── Attendance
└── Tenant C
```

Every tenant-owned record must contain `tenant_id`.

Tenant isolation is mandatory at:

1. Authentication context
2. Authorization
3. Service layer
4. Query layer
5. Policies
6. API resources
7. File storage
8. Background jobs
9. Reports
10. Audit logs

Never trust a tenant ID sent by the client. Resolve the tenant from the authenticated context and verify authorization.

---

# 7. V1 Scope

## Included in V1

### Organization
- Company
- Branch
- Department
- Division
- Team
- Position
- Work location

### Employee
- Employee profile
- Employment status
- Contract
- Documents
- Emergency contact
- Organization assignment

### Access
- Authentication
- Role
- Permission
- User management
- Session/device management

### Attendance
- Clock-in
- Clock-out
- attendance history
- schedule
- shift
- late
- early leave
- overtime records
- leave
- permission
- attendance correction workflow

### Fingerprint
- device registry
- device status
- employee/device mapping
- attendance synchronization
- sync logs
- retry handling
- provider abstraction

### Task
- create
- assign
- update
- status
- priority
- deadline
- checklist
- comments
- attachments
- evidence
- progress

### Target
- target definition
- metric
- target value
- period
- assignment
- progress
- achievement
- remaining
- daily required rate
- risk status

### Project
- project
- members
- tasks
- targets
- progress

### KPI
- KPI definition
- weight
- target
- actual
- scoring
- employee/team KPI

### Performance
- performance dashboard
- periodic performance
- achievement
- KPI score
- task score
- attendance indicators

### Customer
- customer profile
- contact
- address
- location
- work history

### Field Work
- job
- location
- checklist
- evidence
- notes
- completion

### Notification
- in-app
- push notification
- task notification
- target warning
- attendance notification
- approval notification

### Reports
- employee
- attendance
- task
- target
- KPI
- performance
- project
- export CSV/XLSX/PDF

### Audit
- authentication events
- permission changes
- employee changes
- task changes
- target changes
- KPI changes
- sensitive data changes
- administrative actions

### SaaS
- plan
- subscription
- limits
- usage
- feature flags
- tenant billing state

---

# 8. V1 Explicitly Excluded

The following are not required for V1 unless separately approved:

- LLM chat
- RAG
- Voice-to-Text
- Text-to-Voice
- AI Agent
- automated AI actions
- complex payroll engine
- accounting
- full CRM
- full marketing automation
- WhatsApp AI automation
- biometric face recognition

The architecture must remain ready for these features.

---

# 9. Future AI Roadmap

## V2 — AI Assistant

Text-based AI assistant.

Example:

> "Ringkas pekerjaan tim saya minggu ini."

AI gathers data through controlled application tools:

```text
get_team()
get_attendance()
get_tasks()
get_targets()
get_kpi()
get_performance()
```

Then generates a summary.

## V2.1 — AI Monitoring

The system can detect:

- overdue task
- target risk
- unusual progress
- missing evidence
- attendance anomalies
- performance trend

The AI must not make high-impact employment decisions automatically.

## V2.2 — RAG

Knowledge base:

- SOP
- company policies
- manuals
- employee handbook
- technical documentation
- FAQ

Use PostgreSQL + pgvector initially.

## V3 — AI Agent

Controlled actions:

- create task
- update task
- generate report
- create target proposal
- send notification

Sensitive actions require confirmation/approval.

## V4 — Voice

Voice-to-Text:
- Whisper/faster-whisper or equivalent

Text-to-Voice:
- Piper or equivalent

Voice is future scope.

---

# 10. Core Business Workflow

## Employee Lifecycle

```text
Company
→ Employee Registration
→ Organization Assignment
→ Role Assignment
→ Schedule
→ Attendance
→ Task
→ Target
→ KPI
→ Performance
```

## Task Lifecycle

```text
Draft
→ Assigned
→ Accepted
→ In Progress
→ Waiting
→ Review
→ Completed
```

Alternative terminal states:

```text
Rejected
Cancelled
Overdue
```

## Target Lifecycle

```text
Created
→ Assigned
→ Active
→ Monitoring
→ Completed / Expired
```

## Approval Lifecycle

```text
Draft
→ Submitted
→ Approved / Rejected
```

---

# 11. Target Engine

Target is a first-class business object.

Each target contains:

- target name
- metric
- target value
- unit
- period
- start date
- end date
- employee/team/project
- actual value
- progress
- achievement percentage
- remaining value
- required daily rate
- status
- risk level

Example:

```text
Target: Customer Installation
Target Value: 100
Actual: 70
Remaining: 30
Days Remaining: 5
Required Daily Rate: 6
Achievement: 70%
```

Risk logic should be configurable.

Default conceptual statuses:

- ON_TRACK
- AT_RISK
- CRITICAL
- COMPLETED
- EXPIRED

The system must show the calculation basis.

---

# 12. KPI Engine

KPI definition:

```text
name
description
metric
unit
weight
target
minimum
maximum
scoring_method
period
```

Example:

```text
Installation
Weight: 30%

Response Time
Weight: 20%

Customer Complaint
Weight: 20%

Attendance
Weight: 10%

Task Completion
Weight: 20%
```

Total KPI weight should normally equal 100% for an active KPI template.

Scoring must be configurable rather than hard-coded.

---

# 13. Performance Engine

Performance score may combine:

- KPI
- task achievement
- target achievement
- attendance indicators
- project achievement

Weights must be configurable.

Do not hard-code one universal employee performance formula.

Performance calculations must be versioned so historical scores do not unexpectedly change when configuration changes.

---

# 14. Attendance System

## Manual

Employee can use Flutter:

```text
CHECK IN
CHECK OUT
```

Optional validation:

- GPS
- geofence
- selfie
- device binding

## Fingerprint

Flow:

```text
Fingerprint Device
→ Provider API/SDK
→ Fingerprint Adapter
→ Sync Service
→ Attendance Event
→ Attendance Record
```

Implement an abstraction:

```php
interface FingerprintProvider
{
    public function testConnection(): ProviderResult;
    public function syncEmployees(): ProviderResult;
    public function fetchAttendance(): ProviderResult;
    public function enrollEmployee(array $data): ProviderResult;
}
```

The X Solutions implementation should be isolated in:

```text
app/Integrations/Fingerprint/XSolutions/
```

Do not hard-code X Solutions logic throughout attendance controllers.

Because the exact public API/SDK specification may differ by X Solutions device/model, implement the adapter based on the actual vendor documentation supplied during development.

---

# 15. Fingerprint Sync Requirements

The system must support:

- device registration
- device authentication/configuration
- device health/status
- employee mapping
- pull attendance logs
- duplicate protection
- idempotent synchronization
- sync cursor/time window
- retry
- error log
- last successful sync
- manual sync
- scheduled sync

Attendance records should have an external identity:

```text
source
source_device_id
source_event_id
```

Use a unique constraint where possible to prevent duplicate imports.

---

# 16. Task Management

## Task Fields

```text
id
tenant_id
project_id
customer_id
assigned_by
priority
title
description
status
start_at
due_at
completed_at
target_value
target_unit
progress_value
progress_percent
created_by
updated_by
created_at
updated_at
```

## Checklist

```text
task_checklists
- task_id
- title
- is_completed
- completed_by
- completed_at
- sort_order
```

## Evidence

```text
task_evidences
- task_id
- uploaded_by
- file_id
- type
- description
- captured_at
- latitude
- longitude
```

---

# 17. Project Management

Project contains:

- code
- name
- description
- manager
- members
- customer
- start
- deadline
- status
- budget (optional)
- target
- progress

Project progress can be derived from tasks but must not be recalculated expensively on every request. Use cached/aggregated values where appropriate.

---

# 18. Customer Management

Customer:

- code
- name
- company name
- email
- phone
- address
- latitude
- longitude
- status
- assigned account/team

Customer is designed as a lightweight operational CRM, not a full sales CRM in V1.

---

# 19. Field Work

Field work task can include:

- customer
- site
- coordinates
- technician
- scheduled time
- checklist
- evidence
- work notes
- customer signature
- completion status

The mobile app must support offline-friendly operation for field workflows where practical.

Offline data should use a local queue and synchronize safely when connectivity returns.

---

# 20. Notification Engine

Notification types:

- task assigned
- task deadline approaching
- task overdue
- target warning
- attendance issue
- leave submitted
- leave approved
- leave rejected
- task review requested
- announcement

Channels:

1. In-app
2. Firebase Cloud Messaging
3. Email (optional)
4. Future WhatsApp integration

Notification records must contain:

```text
tenant_id
user_id
type
title
body
data
read_at
created_at
```

---

# 21. Dashboard Requirements

## Owner Dashboard

Cards:

- employee count
- present
- absent
- active tasks
- overdue tasks
- target achievement
- KPI average
- project progress

Charts:

- attendance trend
- task trend
- target trend
- department performance
- branch comparison

Alerts:

- overdue tasks
- target risk
- attendance exceptions
- pending approvals

## Manager Dashboard

Focus:

- my team
- today's tasks
- target
- attendance
- KPI
- pending review

## Employee Dashboard

Focus:

- attendance
- today's tasks
- target
- progress
- notifications

---

# 22. UI / UX Direction

## Brand

Use the supplied RMIH logo.

Product name:

**RMIH**

Subtitle:

**Resource Management Integrated Human**

## Visual style

- professional
- enterprise
- modern
- clean
- responsive
- accessible
- not overly decorative
- information-dense but readable

## Color direction

Use the logo as the visual source of truth.

Recommended palette:

- deep navy
- blue
- light blue
- white
- neutral gray
- orange/yellow as an accent
- green for success
- amber for warning
- red for critical

Do not use too many gradients.

## Typography

Use a clean sans-serif font.

Recommended:
- Inter

## Components

Create reusable components:

- Button
- Input
- Select
- Date picker
- Search
- Filter
- Data table
- Stat card
- Badge
- Modal
- Drawer
- Tabs
- Dropdown
- Empty state
- Skeleton loader
- Toast
- Confirmation dialog
- Pagination

---

# 23. Landing Page Requirements

Landing page sections:

## Hero

Headline:

> **Manage People. Track Work. Achieve Targets.**

Subheadline:

> RMIH integrates employees, attendance, tasks, targets, KPI, and performance into one secure workforce platform.

CTA:

- Start Free Trial
- Request Demo

## Trust / Value

Show:

- Workforce
- Attendance
- Task
- Target
- KPI
- Performance

## Product Preview

Dashboard mockup.

## Feature Section

Cards:

1. Employee Management
2. Smart Attendance
3. Fingerprint Integration
4. Task Management
5. Target Monitoring
6. KPI & Performance
7. Field Workforce
8. Reports
9. Mobile App
10. Secure Multi-Tenant SaaS

## Workflow

```text
People
→ Attendance
→ Work
→ Target
→ Performance
```

## Mobile Preview

Show Flutter APK screens.

## Security

Highlight:

- tenant isolation
- role-based access
- audit trail
- encrypted transport
- backup
- secure API

## Pricing

Display SaaS plans.

## CTA

> Start managing your workforce with RMIH.

---

# 24. Flutter Mobile Requirements

Flutter application should support:

- login
- forgot password
- dashboard
- profile
- attendance
- schedule
- tasks
- task details
- checklist
- evidence
- target
- notifications
- reports (role dependent)
- employee directory (permission dependent)

Bottom navigation for employee:

```text
Home
Tasks
Target
Notifications
Profile
```

Attendance can be a prominent action on Home.

---

# 25. Flutter Architecture

Use a feature-oriented structure:

```text
lib/
├── core/
│   ├── config/
│   ├── network/
│   ├── auth/
│   ├── storage/
│   ├── permissions/
│   ├── notifications/
│   └── theme/
│
├── features/
│   ├── auth/
│   ├── dashboard/
│   ├── attendance/
│   ├── tasks/
│   ├── targets/
│   ├── notifications/
│   ├── profile/
│   ├── customers/
│   └── field_work/
│
└── shared/
    ├── widgets/
    ├── models/
    └── utils/
```

Use:

- repository pattern
- service layer
- DTO/model separation where useful
- centralized API client
- secure local token storage
- typed error handling

---

# 26. Laravel Architecture

Recommended modular monolith:

```text
app/
├── Domain/
│   ├── Tenant/
│   ├── Organization/
│   ├── Employee/
│   ├── Attendance/
│   ├── Fingerprint/
│   ├── Task/
│   ├── Target/
│   ├── Project/
│   ├── KPI/
│   ├── Performance/
│   ├── Customer/
│   ├── Notification/
│   ├── Report/
│   └── SaaS/
│
├── Application/
│   ├── Actions/
│   ├── Services/
│   └── DTOs/
│
├── Integrations/
│   └── Fingerprint/
│       └── XSolutions/
│
└── Http/
    ├── Controllers/
    ├── Requests/
    └── Resources/
```

Do not put business logic directly inside controllers.

---

# 27. Database

Primary database:

**PostgreSQL**

Use:

- foreign keys
- indexes
- check constraints where useful
- unique constraints
- JSONB only where appropriate
- transactions
- soft deletion where business-appropriate
- timestamps
- UUID/ULID strategy consistently

Suggested primary IDs:

Use ULID/UUID for public identifiers to reduce predictable sequential IDs in public APIs.

Internal integer IDs can be used only if there is a clear reason, but the public API must not expose sensitive predictable identifiers.

---

# 28. Initial Database Tables

Core:

```text
tenants
tenant_settings
tenant_domains
subscription_plans
subscriptions
subscription_usages

users
roles
permissions
role_permissions
user_roles
user_sessions

companies
branches
departments
divisions
teams
positions
work_locations

employees
employee_contracts
employee_documents
employee_emergency_contacts
employee_histories

work_shifts
work_schedules
attendance_records
attendance_events
attendance_corrections
leave_types
leave_requests
overtime_requests

fingerprint_devices
fingerprint_device_users
fingerprint_sync_runs
fingerprint_sync_errors
fingerprint_mappings

projects
project_members
tasks
task_assignees
task_checklists
task_comments
task_attachments
task_evidences
task_status_histories

targets
target_assignments
target_progress
target_periods

kpi_templates
kpi_metrics
kpi_assignments
kpi_results
performance_periods
performance_results

customers
customer_contacts
customer_locations
field_jobs

notifications
notification_preferences
notification_logs

files
audit_logs
activity_logs
system_events
```

Future AI:

```text
ai_conversations
ai_messages
ai_tool_calls
knowledge_documents
knowledge_chunks
knowledge_embeddings
ai_actions
ai_action_approvals
```

Do not create AI tables in V1 unless required for future migration strategy; migration stubs can be planned.

---

# 29. Indexing Strategy

Index all high-volume queries based on real access patterns.

Common composite indexes:

```text
(tenant_id, created_at)
(tenant_id, status)
(tenant_id, employee_id)
(tenant_id, user_id)
(tenant_id, branch_id)
(tenant_id, department_id)
(tenant_id, due_at)
(tenant_id, period_start, period_end)
```

Attendance:

```text
tenant_id + employee_id + attendance_date
tenant_id + device_id + event_time
```

Tasks:

```text
tenant_id + assigned_to + status
tenant_id + due_at + status
tenant_id + project_id + status
```

Targets:

```text
tenant_id + assignee_id + period
tenant_id + status + end_date
```

Avoid unnecessary indexes. Every index has write/storage cost.

---

# 30. Scalability Strategy

Target architecture should be capable of scaling toward millions of records.

## Stage 1

```text
Load Balancer
→ Laravel
→ Redis
→ PostgreSQL
```

## Stage 2

```text
Load Balancer
→ Laravel instances
→ Redis
→ Queue workers
→ PostgreSQL Primary
→ Read Replica
```

## Stage 3

Add:

- object storage
- dedicated queue workers
- read replicas
- database partitioning
- analytics workload separation
- background report generation
- specialized services where justified

Do not introduce microservices prematurely.

Use a modular monolith first.

---

# 31. Large Data Strategy

High-volume tables likely include:

- attendance_events
- audit_logs
- activity_logs
- notification_logs
- system_events

For very large datasets, use PostgreSQL partitioning by time and/or another carefully chosen key.

Example:

```text
attendance_events_2026_01
attendance_events_2026_02
...
```

Partitioning must be introduced based on measured scale and query patterns.

Do not partition every table by default.

---

# 32. Queue Architecture

Use Redis-backed queues for:

- fingerprint synchronization
- notifications
- email
- report generation
- file processing
- aggregation
- future AI jobs

Never perform expensive report generation synchronously in an HTTP request.

Example:

```text
User requests Excel
→ Queue Job
→ Generate file
→ Store file
→ Notify user
→ Download
```

---

# 33. Cache Strategy

Cache:

- tenant configuration
- permission maps
- dashboard aggregates
- static lookup data
- subscription limits

Do not cache sensitive data without an explicit invalidation strategy.

Cache keys must contain tenant context where relevant:

```text
tenant:{tenantId}:dashboard:{period}
```

---

# 34. API Design

Base:

```text
/api/v1
```

Auth:

```text
POST /api/v1/auth/login
POST /api/v1/auth/logout
POST /api/v1/auth/refresh
GET  /api/v1/auth/me
```

Employees:

```text
GET    /api/v1/employees
POST   /api/v1/employees
GET    /api/v1/employees/{id}
PUT    /api/v1/employees/{id}
DELETE /api/v1/employees/{id}
```

Attendance:

```text
POST /api/v1/attendance/check-in
POST /api/v1/attendance/check-out
GET  /api/v1/attendance
GET  /api/v1/attendance/today
```

Tasks:

```text
GET    /api/v1/tasks
POST   /api/v1/tasks
GET    /api/v1/tasks/{id}
PUT    /api/v1/tasks/{id}
POST   /api/v1/tasks/{id}/complete
POST   /api/v1/tasks/{id}/evidence
```

Targets:

```text
GET  /api/v1/targets
POST /api/v1/targets
GET  /api/v1/targets/{id}
POST /api/v1/targets/{id}/progress
```

KPI:

```text
GET  /api/v1/kpis
POST /api/v1/kpis
GET  /api/v1/kpis/{id}
```

Reports:

```text
GET /api/v1/reports/attendance
GET /api/v1/reports/tasks
GET /api/v1/reports/targets
GET /api/v1/reports/performance
```

Every endpoint must have authorization and tenant isolation.

---

# 35. API Standards

All API responses should follow a consistent envelope.

Success example:

```json
{
  "success": true,
  "data": {},
  "meta": {}
}
```

Error example:

```json
{
  "success": false,
  "message": "You are not authorized to access this resource.",
  "errors": {}
}
```

Use pagination.

Do not return unbounded collections.

---

# 36. Security Requirements

Security is a core requirement.

## Authentication

- secure password hashing
- session/token expiration
- logout/revocation
- device/session visibility
- optional 2FA
- brute-force/rate limiting
- secure password reset

## Authorization

Use:

- roles
- permissions
- policies
- tenant context
- ownership checks

Never rely on hidden UI elements for authorization.

## API Security

Protect against:

- broken object-level authorization
- broken function-level authorization
- mass assignment
- injection
- unrestricted resource consumption
- security misconfiguration
- unsafe file upload
- SSRF
- insecure direct object references
- excessive data exposure

## Data Security

- HTTPS/TLS
- encrypted secrets
- secure environment configuration
- database access restrictions
- backup encryption
- least privilege
- secure object storage
- signed URLs for private files

---

# 37. File Upload Security

Allowed file types must be explicit.

Validate:

- MIME
- extension
- file size
- actual file signature where possible

Store private files outside public web root.

Use signed temporary URLs.

Generate safe filenames.

Never trust original filenames.

For enterprise deployments, optionally scan uploads for malware.

---

# 38. Audit Log Requirements

Audit sensitive events:

```text
login
logout
failed login
role change
permission change
employee create/update/delete
salary-related change if payroll is later added
attendance correction
task reassignment
target modification
KPI modification
subscription change
tenant setting change
file access where required
```

Audit record:

```text
tenant_id
actor_user_id
action
entity_type
entity_id
old_values
new_values
ip_address
user_agent
request_id
created_at
```

Audit logs should be append-only from normal application roles.

---

# 39. Observability

Production should have:

- structured logs
- request ID
- job ID
- error tracking
- performance monitoring
- queue monitoring
- database monitoring
- uptime monitoring

Sensitive personal data must not be written into logs unnecessarily.

---

# 40. Backup & Disaster Recovery

Minimum:

- automated PostgreSQL backup
- backup retention policy
- encrypted backup
- off-server backup
- periodic restore test

Enterprise:

- point-in-time recovery
- cross-region backup where required
- documented RPO/RTO

Example targets should be configurable per deployment, not hard-coded as a promise.

---

# 41. Privacy

RMIH stores personal employee data.

The system must support:

- least privilege
- purpose-based access
- auditability
- data retention policy
- deletion/anonymization workflow where legally applicable
- private file storage
- restricted exports

Do not expose employee personal data in public APIs.

---

# 42. SaaS Plans

Initial commercial proposal:

## STARTER

**Rp299.000/month**

Suggested limit:

- up to 10 employees
- 1 company
- 1 branch
- attendance
- task
- target
- dashboard
- mobile app
- basic report

## BUSINESS

**Rp799.000/month**

Suggested limit:

- up to 30 employees
- multiple teams
- fingerprint integration
- KPI
- performance
- project
- customer
- field work
- advanced reports
- push notifications

## PROFESSIONAL

**Rp1.499.000/month**

Suggested limit:

- up to 75 employees
- multi-branch
- advanced KPI
- advanced target
- field workforce
- API
- automation
- larger storage
- priority support

## ENTERPRISE

**Starting from Rp3.500.000/month**

Custom:

- custom employee volume
- multi-company
- dedicated environment
- custom integration
- SLA
- dedicated support
- private deployment
- advanced security

Pricing is an initial product proposal and must be validated commercially before launch.

---

# 43. SaaS Usage Metering

Track:

- active employees
- branches
- storage
- API requests
- fingerprint devices
- monthly reports
- future AI usage
- future voice minutes

Usage must be recorded in a way that allows billing changes later.

Do not hard-code plan limits into controllers.

Use a central entitlement service:

```text
EntitlementService
- canUseFeature()
- remaining()
- limit()
- consume()
```

---

# 44. Subscription Architecture

Entities:

```text
subscription_plans
plan_features
subscriptions
subscription_items
usage_counters
invoices
payments
```

Payment provider can be integrated later.

V1 can support manual billing state if automatic payment integration is not ready.

---

# 45. Landing Page Copy

## Hero

**Manage People. Track Work. Achieve Targets.**

RMIH menyatukan data karyawan, absensi, tugas, target, KPI, dan performa dalam satu platform workforce management yang terintegrasi.

CTA:

**Mulai Sekarang**

Secondary:

**Request Demo**

## Feature Copy

### Workforce Management
Kelola data karyawan, organisasi, jabatan, dan struktur perusahaan.

### Smart Attendance
Integrasikan presensi mobile dan perangkat fingerprint.

### Task Management
Tetapkan pekerjaan, deadline, checklist, evidence, dan status.

### Target Monitoring
Pantau target secara real-time dan identifikasi pekerjaan yang berisiko terlambat.

### KPI & Performance
Hubungkan pekerjaan dan target dengan indikator kinerja.

### Mobile Workforce
Berikan akses kepada karyawan dan teknisi melalui aplikasi Android.

### Secure SaaS
Multi-tenant, role-based access, audit trail, dan arsitektur siap berkembang.

---

# 46. Mockup Page List

Kiro implementation should produce these pages.

## Web

1. Login
2. Forgot Password
3. Dashboard
4. Employee List
5. Employee Detail
6. Organization
7. Branch
8. Department
9. Team
10. Position
11. Attendance Dashboard
12. Attendance Calendar
13. Attendance Detail
14. Fingerprint Devices
15. Fingerprint Sync Logs
16. Work Shifts
17. Schedules
18. Leave
19. Tasks
20. Task Kanban
21. Task Detail
22. Projects
23. Project Detail
24. Targets
25. Target Detail
26. KPI
27. KPI Template
28. Performance
29. Customers
30. Customer Detail
31. Field Jobs
32. Notifications
33. Reports
34. Audit Logs
35. Settings
36. Subscription
37. Usage
38. User Management
39. Roles
40. Permissions

## Flutter

1. Splash
2. Login
3. Home
4. Attendance
5. Task List
6. Task Detail
7. Checklist
8. Evidence
9. Target
10. Notifications
11. Profile
12. Schedule
13. Customer
14. Field Job
15. Offline Sync

---

# 47. UX Acceptance Criteria

The interface must:

- work on desktop and mobile
- provide loading states
- provide empty states
- provide error states
- provide confirmation for destructive actions
- prevent accidental duplicate submissions
- use pagination
- provide search/filter for large datasets
- maintain consistent status badges
- provide accessible form labels
- provide clear validation messages

---

# 48. Performance Requirements

Initial targets:

- normal API response should generally be fast enough for interactive use
- heavy reports must be queued
- large lists must be paginated
- dashboard aggregation should use caching/pre-aggregation when required
- mobile payloads should be minimized
- images should be compressed/resized
- files should use object storage for scale

Do not promise a fixed response time until real load testing is completed.

---

# 49. Testing Strategy

## Unit Tests

For:

- target calculation
- KPI calculation
- permissions
- subscription limits
- attendance calculations
- task state transitions

## Feature Tests

For:

- login
- employee creation
- task creation
- target progress
- attendance
- leave
- reports
- tenant isolation

## Security Tests

Verify:

- Tenant A cannot access Tenant B
- Employee cannot modify another employee
- Supervisor cannot access unauthorized admin endpoints
- API cannot bypass policy through direct IDs
- private files cannot be accessed without authorization

## Integration Tests

Fingerprint:

- connection
- sync
- duplicate event
- failed sync
- retry

## Flutter

- unit tests
- widget tests
- integration tests
- offline sync tests

---

# 50. Definition of Done

A feature is complete only when:

1. Database migration exists
2. Model exists
3. Validation exists
4. Authorization exists
5. Service/action exists
6. API endpoint exists if required
7. API resource exists
8. Web UI exists if applicable
9. Flutter UI exists if applicable
10. Tests exist
11. Audit behavior exists for sensitive actions
12. Tenant isolation is tested
13. Error handling exists
14. Loading/empty states exist
15. Documentation is updated

---

# 51. Kiro AI Development Rules

Kiro AI must follow these rules during implementation.

## Rule 1 — Do not build everything in one pass

Develop in phases.

## Rule 2 — Do not invent requirements

If a requirement is not defined, use the architecture and ask for clarification only when the decision materially affects data/security/business behavior.

## Rule 3 — Preserve tenant isolation

Every tenant-owned query must be tenant scoped.

## Rule 4 — Never trust frontend authorization

Backend authorization is mandatory.

## Rule 5 — No business logic in controllers

Controllers should remain thin.

## Rule 6 — Use services/actions

Business logic belongs in domain/application services.

## Rule 7 — API first

Flutter communicates only through the API.

## Rule 8 — Keep fingerprint integration isolated

Do not couple attendance logic directly to X Solutions.

## Rule 9 — Future AI must use tools/services

AI must never receive unrestricted database credentials.

## Rule 10 — Use migrations

Never manually create production schema outside migration/version control.

## Rule 11 — No hard-coded plan limits

Use entitlement/feature configuration.

## Rule 12 — Avoid premature microservices

Start with modular monolith.

## Rule 13 — Write tests with features

Do not postpone all testing until the end.

## Rule 14 — Protect personal data

Minimize logs, exports, and API responses.

## Rule 15 — Use background jobs

Long-running tasks must not block HTTP requests.

---

# 52. Suggested Kiro Project Structure

```text
rmih/
├── backend/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── tests/
│   └── ...
│
├── mobile/
│   ├── lib/
│   ├── test/
│   └── ...
│
├── docs/
│   ├── PRD.md
│   ├── ARCHITECTURE.md
│   ├── API.md
│   ├── DATABASE.md
│   ├── SECURITY.md
│   └── FINGERPRINT.md
│
└── README.md
```

---

# 53. Kiro Implementation Order

## Sprint 0 — Project Foundation

- repository setup
- Laravel setup
- PostgreSQL
- Redis
- environment configuration
- code style
- CI
- testing
- API versioning
- base exception handling
- logging
- tenant context

## Sprint 1 — Authentication & SaaS

- users
- authentication
- tenants
- roles
- permissions
- policies
- subscriptions
- feature entitlements

## Sprint 2 — Organization & Employee

- company
- branches
- departments
- teams
- positions
- employees
- documents

## Sprint 3 — Attendance

- schedules
- shifts
- attendance
- leave
- correction
- mobile attendance

## Sprint 4 — Fingerprint

- provider interface
- X Solutions adapter
- devices
- mapping
- sync
- logs
- retries

## Sprint 5 — Task

- tasks
- assignment
- checklist
- comments
- attachments
- evidence
- status workflow

## Sprint 6 — Target

- target definitions
- assignments
- progress
- calculations
- risk status

## Sprint 7 — Project & Customer

- project
- project members
- customer
- field jobs

## Sprint 8 — KPI & Performance

- KPI template
- metrics
- weighting
- scoring
- performance periods

## Sprint 9 — Dashboard & Reports

- dashboards
- aggregation
- exports
- queued reports

## Sprint 10 — Flutter Production

- all core mobile features
- push notification
- offline queue
- sync
- security

## Sprint 11 — Security & Load Test

- authorization audit
- tenant isolation test
- load testing
- database indexes
- queue testing
- backup/restore testing

## Sprint 12 — Production

- deployment
- monitoring
- backups
- documentation
- release checklist

---

# 54. Future AI Technical Architecture

When AI is activated:

```text
Flutter/Web
      ↓
Laravel API
      ↓
AI Orchestrator
      ↓
Intent / Tool Selection
      ↓
┌───────────────────────────┐
│ Controlled Application    │
│ Tools                     │
│                           │
│ get_tasks()               │
│ get_targets()             │
│ get_attendance()          │
│ get_performance()         │
│ create_task()             │
│ update_task()             │
│ generate_report()         │
└───────────────────────────┘
      ↓
LLM
      ↓
Response
```

Never:

```text
LLM
 ↓
Raw PostgreSQL credentials
```

---

# 55. Future RAG Architecture

```text
Company Documents
      ↓
Document Processor
      ↓
Chunking
      ↓
Embedding
      ↓
PostgreSQL + pgvector
      ↓
Retriever
      ↓
LLM
```

Sources:

- SOP
- policies
- manuals
- company documents

RAG access must also be tenant-scoped.

Tenant A must never retrieve Tenant B's documents.

---

# 56. Future AI Action Security

AI actions should have risk levels.

### Low Risk

- summarize
- search
- calculate
- generate report

Can execute automatically.

### Medium Risk

- create task
- assign task
- send notification

May require configurable confirmation.

### High Risk

- modify employee sensitive data
- change payroll
- terminate employee
- change permissions
- change subscription

Must require explicit authorized human confirmation.

---

# 57. Future Voice Architecture

```text
Flutter
 ↓
Audio
 ↓
Speech-to-Text
 ↓
AI Orchestrator
 ↓
Tool / LLM
 ↓
Response Text
 ↓
Text-to-Speech
 ↓
Flutter Audio
```

Voice is not part of V1.

---

# 58. Security for Future AI

AI must:

- inherit user permissions
- inherit tenant context
- filter data before model input
- avoid sending unnecessary PII to external providers
- log tool calls
- log action approvals
- prevent prompt injection from changing authorization
- validate tool parameters
- enforce server-side authorization
- use allowlisted tools

The LLM must never be treated as a trusted authorization layer.

---

# 59. Final Product Architecture

```text
                         RMIH SaaS
                            │
             ┌──────────────┴──────────────┐
             │                             │
         WEB ADMIN                     FLUTTER APK
             │                             │
             └──────────────┬──────────────┘
                            │
                       API / V1
                            │
                    Laravel Application
                            │
        ┌───────────────────┼────────────────────┐
        │                   │                    │
   Domain Modules       Integration          SaaS Layer
        │                   │                    │
 HR / Employee         Fingerprint            Tenant
 Attendance             X Solutions            Plan
 Tasks                  Notifications          Subscription
 Targets                                       Usage
 KPI
 Performance
 Projects
 Customer
 Reports
        │
        ├──────── PostgreSQL
        │
        ├──────── Redis
        │
        ├──────── Queue Workers
        │
        └──────── Object Storage

Future:
                    AI Orchestrator
                         │
              ┌──────────┼──────────┐
              │          │          │
             LLM        RAG       Voice
              │          │          │
          AI Tools   pgvector   STT/TTS
```

---

# 60. Launch Acceptance Checklist

Before V1 production:

### Core

- [ ] Tenant system works
- [ ] Login works
- [ ] RBAC works
- [ ] Employee management works
- [ ] Attendance works
- [ ] Fingerprint synchronization works
- [ ] Task works
- [ ] Target works
- [ ] KPI works
- [ ] Performance works
- [ ] Notifications work
- [ ] Reports work
- [ ] Flutter APK works

### Security

- [ ] Tenant isolation tested
- [ ] Authorization tested
- [ ] Rate limiting enabled
- [ ] HTTPS enabled
- [ ] Private file storage enabled
- [ ] Audit log enabled
- [ ] Backup tested
- [ ] Restore tested
- [ ] Secrets protected

### Performance

- [ ] Pagination implemented
- [ ] Heavy reports queued
- [ ] Indexes verified
- [ ] Redis configured
- [ ] Queue workers configured
- [ ] Load testing completed
- [ ] Error monitoring enabled

### Business

- [ ] Pricing configured
- [ ] Subscription limits tested
- [ ] Usage metering tested
- [ ] Trial flow tested
- [ ] Upgrade/downgrade behavior defined
- [ ] Support process defined

---

# 61. Kiro AI Master Instruction

Use the following as the high-level instruction when starting implementation in Kiro:

> Build RMIH (Resource Management Integrated Human), a production-oriented multi-tenant SaaS workforce management platform using Laravel 12, PostgreSQL, Redis, REST API, and Flutter for Android.
>
> Implement the system as a modular monolith with clear domain boundaries. The V1 scope consists of tenant management, authentication, RBAC, organization, employee management, attendance, work schedules, leave, fingerprint integration through a provider abstraction, tasks, projects, targets, KPI, performance, customers, field jobs, notifications, reports, audit logs, subscription plans, and usage limits.
>
> Use API versioning under `/api/v1`. Flutter must communicate with Laravel through the API and must never connect directly to PostgreSQL.
>
> Every tenant-owned database record must be tenant scoped. Never trust a tenant ID supplied by the client. Use authenticated tenant context and backend policies. Implement object-level authorization for every resource.
>
> Keep controllers thin. Use Form Requests, Actions/Services, Policies, Repositories where useful, API Resources, Jobs, Events, and domain services.
>
> Use PostgreSQL as the primary database and Redis for cache, queues, rate limiting, and temporary application data. Use background jobs for fingerprint synchronization, notifications, report generation, and other expensive operations.
>
> Implement X Solutions fingerprint integration behind a `FingerprintProvider` interface. Do not spread vendor-specific code through the attendance domain. The exact X Solutions API/SDK must be implemented only according to the vendor documentation supplied to the project.
>
> Build responsive enterprise UI for the web and a production-ready Flutter Android application. Prioritize clean UX, reusable components, loading states, empty states, validation, error handling, pagination, and mobile usability.
>
> Build target management as a first-class domain. Every target must support metric, value, period, assignment, progress, achievement percentage, remaining value, required daily rate, and configurable risk status.
>
> KPI and performance calculations must be configurable and versioned. Never hard-code a universal performance formula.
>
> Include comprehensive automated tests for tenant isolation, RBAC, policies, target calculations, KPI calculations, attendance, task workflow, subscription limits, and fingerprint synchronization.
>
> Do not implement LLM, RAG, Voice-to-Text, or Text-to-Voice in V1. However, structure services and data boundaries so these capabilities can be introduced later. Future AI must communicate through controlled application tools and must never have unrestricted database access.
>
> Do not introduce microservices prematurely. Keep V1 as a modular monolith with clean boundaries and background workers. The architecture must be capable of evolving toward multiple application servers, read replicas, partitioned high-volume tables, object storage, analytics infrastructure, and specialized services as scale increases.
>
> Do not invent requirements. If an implementation decision materially affects security, data integrity, billing, or business behavior and is not defined in the PRD, document the decision and request clarification.
>
> Implement the project incrementally according to the sprint order in this PRD. After each sprint, ensure migrations, tests, API documentation, and implementation notes are updated.

---

# 62. Definition of Product Success

RMIH V1 is successful when a company can manage the complete operational loop:

```text
Employee
   ↓
Attendance
   ↓
Assignment
   ↓
Task
   ↓
Target
   ↓
Progress
   ↓
KPI
   ↓
Performance
   ↓
Report
```

from a single platform, with the same data available through the web dashboard and Flutter mobile application.

The long-term product evolution is:

```text
RMIH V1
Workforce Platform

        ↓

RMIH V2
AI-Assisted Workforce

        ↓

RMIH V3
AI-Monitored Workforce

        ↓

RMIH V4
AI-Agent Workforce
```

The core principle remains:

> **AI assists and automates operations, but authorization and high-impact decisions remain under human control.**

---

# 63. Implementation Status & Release Updates (Rozitech HR Edition)

### 63.1. Current System Readiness
- **Core Platform:** Multi-tenant SaaS architecture active on Laravel 12 + PostgreSQL + Redis.
- **Automated Test Suite:** 57 tests passed with 306 assertions covering Auth, Attendance, Corrections, Overtime, Tasks, Projects, Targets, KPI & Templates, Fingerprint Devices, Self-Registration, Subscription lockouts, AI Assistant, and Web Smoke tests.
- **Organization Structure Hub:**
  - Company Settings (`/settings/company`) with auto-initialization from tenant credentials.
  - Organization Hub (`/organization`) displaying live metrics for branches, departments, positions, teams, and employees.
  - Full interactive Web management for Branches (`/branches`), Departments (`/departments`), Positions (`/positions`), Teams (`/teams`), and Geofencing Work Locations (`/work-locations`).
  - Separation of API endpoint namespaces (`api.`) preventing route collision with Web views.
- **Biometric & Hardware Sync:**
  - Fingerprint management dashboard (`/fingerprint`) with device registration modal, online/offline status monitoring, manual log synchronization, connection testing, and PIN mapping.
- **Employee & Attendance Lifecycle:**
  - Self-service & Admin employee records, contract history, document tracking, emergency contacts.
  - Geofenced attendance check-in/out, attendance calendar, leaves approval workflow, overtime requests, and attendance corrections.
- **Performance & Accountability Loop:**
  - Task board & Kanban flow (`/tasks`, `/tasks/kanban`), checklist validation, evidence attachments.
  - Target tracking (`/targets`) with required daily pace calculations and risk categorization.
  - KPI template builder (`/kpi`) with weight validation (50/25/25 presets) and score calculations.
- **AI Assistant Integration:**
  - Native conversational assistant (`/ai/assistant`) powered by OpenRouter LLM and enterprise context services.
  - Clean natural output formatting without robotic disclaimers.
  - Timezone aligned to `Asia/Jakarta` (WIB) with real-time ticking clock in the application header.