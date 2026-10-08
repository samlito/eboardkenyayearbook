# PART 9 — ICT ONE-PAGER

**Document Status:** [READY TO USE] unless marked otherwise
**Prepared by:** Losvinca
**Target Audience:** ICT Directors, ICT Managers, Chief Information Officers, Systems Administrators
**Format:** Single-page sales sheet (A4 or Letter, one side)

---

## ONE-PAGER COPY

---

# E-Board — Technical Overview for ICT

### A Governance Platform Your Infrastructure Team Can Deploy, Secure, and Support

E-Board is a self-hosted, Laravel-based board governance application built on a standard LAMP/LEMP stack. It gives your ICT department full control over deployment, data, security, and maintenance — no vendor-hosted black box, no external API dependencies for core functionality, no proprietary infrastructure requirements.

---

## Architecture & Technology Stack

| Component | Technology | Notes |
|---|---|---|
| **Backend Framework** | Laravel 9/10 | PHP 8+ required |
| **Database** | MySQL | Standard relational database; compatible with existing MySQL infrastructure |
| **Frontend** | Bootstrap 5, Alpine.js, Tailwind CSS | Server-rendered views; no heavy SPA framework dependency |
| **Authentication** | Session-based with OTP 2FA | 6-digit email OTP, 5-minute expiry, rate-throttled |
| **Authorization** | Spatie Laravel Permission | Industry-standard RBAC package for Laravel |
| **Hosting** | Self-hosted (LAMP/LEMP) or cloud | Your infrastructure, your control |

[CURRENT PRODUCT CAPABILITY — all components listed above are in production use]

---

## Security

### Authentication & Access Control [CURRENT PRODUCT CAPABILITY]
- **OTP-Based Two-Factor Authentication** — 6-digit codes delivered via email with 5-minute expiry and request throttling
- **Account Lockout** — Automatic lockout after configurable failed login attempts
- **Forced Password Change** — Enforce password resets for new or compromised accounts
- **User Status Enforcement** — Active/inactive status controls with immediate effect on access
- **10 System Roles** — Superadmin, Admin, CEO, Corporation Secretary, Director, Division Head, DD Internal Audit, DD ICT, Board Member, Officer
- **Granular Permissions** — Spatie-based RBAC with grouped permission management; permissions configurable per role

### Application Security [CURRENT PRODUCT CAPABILITY]
- **CSRF Protection** — Laravel's built-in CSRF token verification on all state-changing requests
- **Encryption** — Laravel's encryption facilities for sensitive data
- **Input Validation** — Server-side validation on all form submissions
- **File Upload Controls** — Configurable upload size limits (up to 20MB), format validation

### Audit & Logging [CURRENT PRODUCT CAPABILITY]
- **Comprehensive Audit Trail** — Automatic logging of all INSERT, UPDATE, and DELETE operations with old and new values
- **Request Metadata** — IP address, user agent, and authenticated user captured with every log entry
- **Security Event Logging** — Failed logins, OTP requests, and security-relevant actions recorded
- **Filterable Audit Viewer** — Filter by date range, user, action type; view in application or export as PDF
- **Meeting-Scoped Audit (V2)** — Dedicated audit view scoped to individual meetings for focused governance review

---

## Deployment & Configuration

### Infrastructure Requirements [CURRENT PRODUCT CAPABILITY]
- **Web Server:** Apache or Nginx
- **PHP:** Version 8.0 or higher with standard extensions (OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath, Fileinfo, GD)
- **Database:** MySQL 5.7+ or MySQL 8.0
- **Email:** SMTP server access (configurable through the application)
- **Storage:** Local filesystem for document storage; standard Laravel storage configuration
- **SSL:** TLS certificate (recommended; standard for any production web application)

[REQUIRES LOSVINCA INPUT: Minimum server specifications — RAM, CPU, disk space recommendations for various institution sizes]

### Initial Setup [CURRENT PRODUCT CAPABILITY]
- **6-Step Setup Wizard** — Guided configuration covering: (1) Institution identity, (2) Branding and visual identity, (3) Workflow configuration, (4) Role label customization, (5) Email/SMTP configuration with test email, (6) Administrator account creation
- **White-Label Branding** — Configurable logos (header, footer, login), institutional colours, favicon, CEO signature image, letterhead background, and login page background
- **47 Kenyan Counties Pre-Loaded** — County reference data available out of the box
- **Financial Year Configuration** — Kenyan fiscal year (July-June) built in

### Email Configuration [CURRENT PRODUCT CAPABILITY]
- **SMTP Integration** — Standard SMTP configuration (host, port, encryption, credentials)
- **Test Email Function** — Verify email delivery before going live
- **33+ Email Templates** — Pre-built templates covering the full governance workflow (notices, paper assignments, review notifications, meeting invitations, resolution assignments)
- **7 Mailable Classes** — Structured Laravel mailables for reliable, maintainable email delivery

---

## Role-Based Access Control Detail

E-Board implements Spatie Laravel Permission with 10 predefined roles. Permissions are organized into logical groups and can be assigned or modified per role through the administrative interface. [CURRENT PRODUCT CAPABILITY]

| Role | Typical Access Scope |
|---|---|
| Superadmin | Full system access, all configuration |
| Admin | Institution administration, user management |
| CEO | Approval workflows, executive dashboard, paper review |
| Corporation Secretary | Meeting management, board member management, full meeting workflow |
| Director | Paper review, meeting participation, committee membership |
| Division Head | Divisional oversight, paper preparation coordination |
| DD Internal Audit | Audit trail access, compliance review |
| DD ICT | System configuration, technical administration |
| Board Member | Board portal access, papers, notices, declarations, meetings |
| Officer | Paper preparation, assigned tasks, meeting support |

---

## Data Management

- **Database:** All data stored in MySQL with standard relational schema; compatible with your existing backup and replication procedures
- **Document Storage:** Files stored on the local filesystem via Laravel's storage abstraction; standard filesystem backup applies
- **PDF Generation:** Server-side PDF generation for almanacs, notices, attendance registers, conflict registers, board papers, and audit logs
- **CSV Export:** Attendance and conflict of interest data exportable as CSV
- **Word Export:** Meeting minutes exportable as Word documents

[CURRENT PRODUCT CAPABILITY]

---

## What Is Not Currently Available

The following capabilities are not part of the current release. They are documented here for transparency so your ICT team can plan accordingly:

[FUTURE ENHANCEMENT — NOT CURRENTLY AVAILABLE]
- SMS notifications (current notification is email and in-app only)
- Video conferencing integration
- Native mobile application (iOS/Android) — current mobile access is via iPad-optimized web portal
- REST API for external system integration
- External calendar synchronization (Google Calendar, Outlook)
- Advanced analytics or business intelligence dashboards
- User-manageable document templates

---

## Support & Maintenance

[REQUIRES LOSVINCA INPUT: Support model details — SLA tiers, response times, update/patch delivery mechanism, escalation contacts, documentation access, training for ICT staff]

---

## Next Steps

Request a technical demonstration and architecture review with your ICT team. We will walk through the deployment process, security configuration, and integration considerations specific to your infrastructure environment.

**Contact Losvinca**
- Phone: [REQUIRES LOSVINCA INPUT]
- Email: [REQUIRES LOSVINCA INPUT]
- Website: [REQUIRES LOSVINCA INPUT]

---

*E-Board by Losvinca — Built on proven open-source technology. Deployed on your infrastructure. Under your control.*
