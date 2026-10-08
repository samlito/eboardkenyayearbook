# TWWDA eBoard System - Version 2 Technical Specification

## Document Information
- **Project**: TWWDA eBoard Management System
- **Version**: 2.0
- **Date**: 2026-05-26
- **Stack**: Laravel 10, PHP 8.1, MariaDB 10.4, Bootstrap 5 (UBold), jQuery DataTables

---

## 1. OVERVIEW

Version 2 introduces a complete 9-stage board meeting workflow system with new database tables (prefixed `twwda_`), enhanced user-directorate mapping, comprehensive audit logging, email notifications, digital signatures, and PDF sticky notes. The system preserves all Version 1 tables and functionality.

### 1.1 Key Objectives
- Map users to directorates and divisions based on role (Director, Deputy Director)
- Implement a 9-stage board meeting preparation workflow
- Board member notification flow with 21-day and 7-day notices
- Comprehensive audit logging (who, when, IP) at every stage
- Digital signatures (stylus/touch) for CEO approvals and conflict declarations
- PDF sticky notes for CEO review of board papers
- Email notifications throughout the workflow
- Standard board paper template based on official TWWDA format

---

## 2. DATABASE SCHEMA DESIGN

All new tables use the `twwda_` prefix to avoid conflicts with Version 1 tables.

### 2.1 User-Directorate Mapping

**Table: `twwda_user_directorate_mappings`**
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| user_id | bigint FK(users.id) | The staff user |
| directorate_id | bigint FK(directorates.id) | Assigned directorate |
| division_id | bigint FK(divisions.id) NULL | Assigned division (for Deputy Directors) |
| role_type | enum('director','deputy_director') | Role type in the directorate |
| is_active | boolean DEFAULT true | Active mapping |
| created_at | timestamp | |
| updated_at | timestamp | |

### 2.2 Meeting Workflow Tables

**Table: `twwda_meetings`** (Main meeting tracking table)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | Auto-increment |
| almanac_schedule_id | bigint FK | Links to almanac schedule |
| board_committee_id | bigint FK | Links to board committee |
| title | varchar(500) | Meeting title |
| meeting_date | date | Scheduled meeting date |
| meeting_time | time | Scheduled time |
| venue | varchar(255) | Meeting venue |
| reference_number | varchar(100) | Unique reference |
| status | enum | See workflow statuses below |
| initiated_by | bigint FK(users.id) | Who initiated |
| initiated_at | timestamp NULL | When initiated |
| current_stage | tinyint DEFAULT 1 | Current workflow stage (1-9) |
| is_started | boolean DEFAULT false | Meeting day started |
| started_at | timestamp NULL | When meeting started |
| started_by | bigint FK NULL | Who started the meeting |
| created_at | timestamp | |
| updated_at | timestamp | |

**Status Enum Values:**
`draft`, `initiated`, `notice_created`, `notice_pending_approval`, `notice_approved`, `notice_rejected`, `officers_notified`, `papers_in_progress`, `papers_submitted`, `papers_level1_review`, `papers_ceo_review`, `papers_approved`, `meeting_prepared`, `meeting_active`, `meeting_completed`

**Table: `twwda_internal_notices`** (Stage 2-3)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| meeting_id | bigint FK(twwda_meetings.id) | |
| reference_number | varchar(100) | Notice reference |
| reference_date | date | Reference date |
| subject | varchar(500) | Notice subject |
| body | text | Notice content (rich text) |
| agenda_items | json | JSON array of agenda items |
| meeting_venue | varchar(255) | |
| meeting_date | date | |
| meeting_time | time | |
| status | enum('draft','submitted','approved','rejected','special_approved') | |
| created_by | bigint FK(users.id) | Corp Secretary or DD Internal Audit |
| submitted_at | timestamp NULL | |
| approved_by | bigint FK NULL | CEO or special approver |
| approved_at | timestamp NULL | |
| is_special_approval | boolean DEFAULT false | CEO unavailable flag |
| special_approval_reason | text NULL | Reason for special approval |
| ceo_signature | text NULL | Base64 signature image |
| version | int DEFAULT 1 | Iteration version |
| created_at | timestamp | |
| updated_at | timestamp | |

**Table: `twwda_internal_notice_comments`** (Stage 3 rejection/resubmission)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| notice_id | bigint FK(twwda_internal_notices.id) | |
| user_id | bigint FK(users.id) | Commenter |
| comment | text | Comment text |
| action | enum('rejected','resubmitted','comment') | |
| iteration | int | Which iteration |
| created_at | timestamp | |
| updated_at | timestamp | |

**Table: `twwda_officer_assignments`** (Stage 4)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| meeting_id | bigint FK(twwda_meetings.id) | |
| user_id | bigint FK(users.id) | Deputy Director assigned |
| director_id | bigint FK(users.id) NULL | Director for visibility |
| agenda_item_number | varchar(50) | e.g., "TPS/1/15/10/2025" |
| agenda_item_title | varchar(500) | Agenda item description |
| notified_at | timestamp NULL | When email sent |
| email_read_at | timestamp NULL | Read receipt timestamp |
| system_read_at | timestamp NULL | System notification read |
| status | enum('pending','notified','read','paper_submitted','paper_approved','paper_rejected') | |
| assigned_by | bigint FK(users.id) | Who made the assignment |
| created_at | timestamp | |
| updated_at | timestamp | |

**Table: `twwda_board_papers`** (Stage 5-7)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| meeting_id | bigint FK(twwda_meetings.id) | |
| assignment_id | bigint FK(twwda_officer_assignments.id) | |
| prepared_by | bigint FK(users.id) | Deputy Director |
| agenda_item_number | varchar(50) | |
| paper_number | varchar(100) | Committee Paper No. |
| title | varchar(500) | Paper title |
| content | longtext | Paper content (structured template) |
| file_path | varchar(500) NULL | Generated PDF path |
| status | enum('draft','submitted','director_review','director_approved','director_rejected','ceo_review','ceo_approved','ceo_rejected') | |
| submitted_at | timestamp NULL | |
| director_approved_by | bigint FK NULL | Director who approved |
| director_approved_at | timestamp NULL | |
| ceo_approved_at | timestamp NULL | |
| ceo_signature | text NULL | Base64 CEO signature |
| display_order | int NULL | Order in meeting agenda |
| is_auto_approved | boolean DEFAULT false | For DD Internal Audit papers |
| version | int DEFAULT 1 | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Table: `twwda_board_paper_annexures`** (Stage 5)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| board_paper_id | bigint FK(twwda_board_papers.id) | |
| title | varchar(255) | Annexure title |
| file_path | varchar(500) | File storage path |
| file_name | varchar(255) | Original filename |
| file_size | bigint | Size in bytes |
| sort_order | int DEFAULT 0 | Display order |
| created_at | timestamp | |
| updated_at | timestamp | |

**Table: `twwda_board_paper_comments`** (Stage 6-7 rejection/resubmission)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| board_paper_id | bigint FK(twwda_board_papers.id) | |
| user_id | bigint FK(users.id) | |
| comment | text | |
| action | enum('rejected','resubmitted','comment','approved') | |
| reviewer_role | enum('director','ceo') | |
| iteration | int | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Table: `twwda_sticky_notes`** (Stage 7 - CEO PDF annotations)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| board_paper_id | bigint FK(twwda_board_papers.id) | |
| user_id | bigint FK(users.id) | CEO |
| page_number | int | PDF page |
| x_position | decimal(8,4) | X coordinate (%) |
| y_position | decimal(8,4) | Y coordinate (%) |
| content | text | Note text |
| color | varchar(20) DEFAULT 'yellow' | Note color |
| created_at | timestamp | |
| updated_at | timestamp | |

**Table: `twwda_meeting_preparations`** (Stage 8)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| meeting_id | bigint FK(twwda_meetings.id) | |
| prepared_by | bigint FK(users.id) | Corp Secretary or DD Internal Audit |
| paper_order | json | Ordered array of paper IDs |
| additional_agenda_items | json NULL | Items not in original notice |
| removed_papers | json NULL | Papers pulled from agenda |
| notes | text NULL | Preparation notes |
| finalized_at | timestamp NULL | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Table: `twwda_meeting_attendance`** (Stage 9)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| meeting_id | bigint FK(twwda_meetings.id) | |
| board_member_id | bigint FK(board_members.id) NULL | For board members |
| user_id | bigint FK(users.id) NULL | For staff attendees |
| attendee_name | varchar(255) NULL | For non-system attendees |
| attendee_designation | varchar(255) NULL | |
| attendance_type | enum('board_member','staff','external') | |
| joined_at | timestamp NULL | When they joined |
| is_present | boolean DEFAULT false | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Table: `twwda_conflict_declarations`** (Stage 9)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| meeting_id | bigint FK(twwda_meetings.id) | |
| board_member_id | bigint FK(board_members.id) | |
| has_conflict | boolean | Yes/No declaration |
| conflict_details | text NULL | Details if conflict exists |
| person_name | varchar(255) NULL | |
| person_address | text NULL | |
| interest_nature | text NULL | |
| interest_value | varchar(100) NULL | |
| interest_ceased_on | date NULL | |
| remarks | text NULL | |
| otp_code | varchar(10) NULL | 6-digit OTP |
| otp_sent_at | timestamp NULL | |
| otp_verified_at | timestamp NULL | |
| signature | text NULL | Base64 stylus signature |
| signed_at | timestamp NULL | |
| ip_address | varchar(45) NULL | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 2.3 Board Member Notification Tables

**Table: `twwda_board_notices`** (21-day and 7-day board notices)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| meeting_id | bigint FK(twwda_meetings.id) | |
| notice_type | enum('21_day','7_day') | |
| reference_number | varchar(100) | |
| reference_date | date | |
| subject | text | |
| body | longtext | Rich text content |
| addressees | json | Board members receiving notice |
| agenda_items | json | Agenda items list |
| status | enum('draft','submitted','approved','rejected','sent') | |
| created_by | bigint FK(users.id) | |
| submitted_at | timestamp NULL | |
| approved_by | bigint FK NULL | CEO |
| approved_at | timestamp NULL | |
| ceo_signature | text NULL | |
| sent_at | timestamp NULL | When email sent to board members |
| sent_by | bigint FK NULL | |
| document_path | varchar(500) NULL | Generated PDF with letterhead |
| version | int DEFAULT 1 | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Table: `twwda_board_notice_comments`** (Rejection/resubmission)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| board_notice_id | bigint FK(twwda_board_notices.id) | |
| user_id | bigint FK(users.id) | |
| comment | text | |
| action | enum('rejected','resubmitted','comment') | |
| iteration | int | |
| created_at | timestamp | |
| updated_at | timestamp | |

### 2.4 Audit Log Enhancement

**Table: `twwda_audit_logs`** (Comprehensive audit trail)
| Column | Type | Description |
|--------|------|-------------|
| id | bigint PK | |
| user_id | bigint FK(users.id) | Actor |
| action | varchar(100) | Action performed |
| module | varchar(100) | Which module/stage |
| entity_type | varchar(100) | Model class name |
| entity_id | bigint | Record ID |
| description | text | Human-readable description |
| old_values | json NULL | Before state |
| new_values | json NULL | After state |
| ip_address | varchar(45) | |
| user_agent | varchar(500) | |
| meeting_id | bigint FK NULL | Related meeting |
| created_at | timestamp | |

### 2.5 Notification Table

**Table: `twwda_notifications`** (In-system notifications)
| Column | Type | Description |
|--------|------|-------------|
| id | uuid PK | |
| user_id | bigint FK(users.id) | Recipient |
| title | varchar(255) | |
| message | text | |
| type | varchar(100) | Notification category |
| link | varchar(500) NULL | Action URL |
| meeting_id | bigint FK NULL | Related meeting |
| read_at | timestamp NULL | |
| email_sent_at | timestamp NULL | When email also sent |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## 3. WORKFLOW STAGES DETAILED SPECIFICATION

### Stage 1: Meeting Initiation
**Route**: `/twwda/meetings/almanac`
**Access Control**:
- DD Internal Audit: Can initiate Audit Sub-Committee meetings only
- Corporation Secretary + Secretariat: Can initiate all committee meetings EXCEPT Audit Sub-Committee
- DD ICT: Can initiate ALL meetings

**Features**:
- Dashboard showing upcoming meetings from active almanac in chronological order
- Analytics: meetings per sub-committee, total scheduled, initiated vs pending
- Initiation changes status to "Meeting Initiated"
- Visual workflow progress bar
- Audit log entry

### Stage 2: Internal Notice Creation
**Route**: `/twwda/meetings/{id}/internal-notice`
**Access Control**:
- DD Internal Audit: Audit committee meetings
- Corporation Secretary: All other committee meetings

**Features**:
- Form fields: reference number, date, subject, meeting venue/date/time, body content, agenda items (dynamic add/remove)
- Countdown timer showing days to meeting
- Rich text editor for body content
- Audit log entry

### Stage 3: Internal Notice CEO Approval
**Route**: `/twwda/ceo/internal-notices`
**Notifications**: Email to pgichuki@tanawwda.go.ke, ceo@tanawwda.go.ke

**Features**:
- CEO reviews and approves/rejects
- Rejection: CEO adds comments, reverts to creator (CS or DD Internal Audit)
- Resubmission workflow with full comment/iteration history
- Approved notice displayed as official document with TWWDA letterhead
- Special approval: CS or DD Internal Audit can approve when CEO unavailable (flagged as special)
- Email + system notification on approval/rejection

### Stage 4: Officer Notification
**Route**: `/twwda/meetings/{id}/assign-officers`
**Access Control**: Corporation Secretary, DD Internal Audit

**Features**:
- List all Directors and Deputy Directors
- Select relevant officers for board paper preparation
- Assign agenda item numbers to each officer
- For DDs with a Director: notification goes to both Director and DD
- DD Internal Audit: auto-assigned (skips to Stage 5)
- Functionality to add additional officers after initial notification
- Email + system notification to assigned officers
- Read receipts: CS dashboard shows who has read their notification
- Dashboard showing all action officers and their workflow status

### Stage 5: Board Paper Preparation
**Route**: `/twwda/meetings/{id}/board-paper`
**Access Control**: Assigned Deputy Directors

**Features**:
- Standard board paper template (from notice_template.docx):
  - TWWDA letterhead header (logo, address, contact)
  - Reference number format: TWWDA/[DEPT]/VOL.X (XX)
  - Date field
  - Addressee section (committee members)
  - Subject line (RE: ...)
  - Body with numbered agenda items
  - Signatory block (Eng. Philip Gichuki, MBS - CEO)
  - CC section
  - KEBS ISO certification footer
- Rich text editor for paper content
- Annexure upload (multiple files)
- Multiple board papers per officer allowed
- Submit for review

### Stage 6: Board Paper Director Approval (Level 1)
**Route**: `/twwda/director/board-papers`
**Access Control**: Directors

**Features**:
- Director reviews DD's board paper
- Approve or reject
- Rejection: Director adds comments, reverts to DD with email + system notification
- Resubmission workflow with iteration history
- Upon approval: Director submits to CEO
- Notifications: CEO, Secretary to CEO, and DD all notified
- DDs without a Director: submit directly to CEO (skip this stage)
- CS Dashboard: visibility of all paper statuses

### Stage 7: Board Paper CEO Approval
**Route**: `/twwda/ceo/board-papers/{meeting_id}`
**Access Control**: CEO

**Features**:
- PDF viewer with sticky note annotations
  - Multiple colors for notes
  - Position notes anywhere on PDF pages
  - Notes visible to paper author for revisions
- Approve or reject
- Rejection workflow:
  - Paper from DD without Director: CEO reverts directly to DD
  - Paper from DD with Director: CEO reverts to Director, who adds optional comments and forwards to DD
- CEO stylus signature on approved papers
- Notifications to all parties on approval/rejection
- CS has visibility of all approved/rejected papers with reasons

### Stage 8: Meeting Preparations
**Route**: `/twwda/meetings/{id}/prepare`
**Access Control**: Corporation Secretary, DD Internal Audit

**Features**:
- View all approved board papers for the meeting
- Drag-and-drop reordering of papers per agenda items
- Pull/remove papers from agenda
- Add new agenda items not in original notice
- DD Internal Audit papers auto-approved (no CEO approval needed)
- Finalize meeting package for board members

### Stage 9: Meeting Day
**Route**: `/twwda/meetings/{id}/live`
**Access Control**: Corporation Secretary, DD Internal Audit (initiate), Board Members (participate)

**Features**:
- Initiate actual meeting
- Capture non-board-member attendees (in-attendance register)
- Board members join and access meeting resources
- Conflict of Interest Declaration:
  - Each board member declares conflicts (yes/no + details)
  - OTP verification via email
  - Stylus signature on declaration form
  - Upon signing: auto-populates attendance register
- Complete workflow timeline from Stage 1 to 9 with all audit logs

---

## 4. BOARD MEMBER NOTIFICATION FLOW

### 4.1 Official Notice (21-Day)
- After meeting initiation, CS/DD IA prepares official notice using TWWDA letterhead template
- Notice sent to CEO for approval
- Upon CEO approval + signature, notice is sent to board members 21 days before meeting
- Email generated on-the-fly (not stored in system)
- Notice attached as PDF in email

### 4.2 Reminder Notice (7-Day)
- Automatic reminder 7 days before meeting
- Includes updated information if any changes

### 4.3 Meeting Date Changes
- CS or DD IA can amend meeting dates
- Re-triggers notice workflow if notices already sent

### 4.4 Board Paper Access
- After Stage 8 completion, board members can view approved papers
- Sticky note comments allowed on papers before meeting day

---

## 5. ROLE-PERMISSION MAPPING

### 5.1 Existing Roles (Spatie)
| Role | Key Permissions |
|------|----------------|
| Superadmin | Full system access |
| Corporation Secretary | Stages 1-4, 8-9, Board notices, Full visibility |
| CEO | Stage 3 (notice approval), Stage 7 (paper approval), Signatures |
| Board Member | Stage 9, View papers, Conflict declarations |
| Division Head | Stage 5-6 (paper prep/review) |
| admin | System administration |

### 5.2 New Permission Groups
- `twwda-meetings`: initiate, view, manage, prepare, start
- `twwda-notices`: create, submit, approve, reject, special-approve
- `twwda-papers`: create, submit, review-l1, review-ceo, approve, reject
- `twwda-assignments`: create, notify, view-receipts
- `twwda-conflicts`: declare, verify, view
- `twwda-board-notices`: create, submit, approve, send

### 5.3 Special Access Rules
- DD Internal Audit: Full access to Audit Sub-Committee meetings, auto-approval for own papers
- DD ICT: Can initiate all meetings
- Secretary to CEO: Receives notification copies (ceo@tanawwda.go.ke)

---

## 6. NOTICE TEMPLATE FORMAT

### 6.1 Header
- TWWDA Logo (left side)
- Organization name: "TANA WATER WORKS DEVELOPMENT AGENCY"
- Address: MAJI HSE, BADEN POWELL ROAD
- P.O. BOX 1292 - 10100 NYERI
- Tel: 061-2032282, Fax: 061-2034118
- Email: info@tanawwda.go.ke
- Website: www.tanawwda.go.ke

### 6.2 Body Structure
- Reference number (e.g., TWWDA/TECH/PLAN/STRA VOL.I (71))
- Date
- Addressees (each with name, title, address, email)
- Subject line (RE: NOTICE OF THE [Nth] [COMMITTEE NAME] COMMITTEE MEETING)
- Notice paragraph with meeting date, venue, time
- Numbered agenda items list
- Closing statement
- Signatory: Eng. Philip Gichuki, MBS - CHIEF EXECUTIVE OFFICER
- CC section

### 6.3 Footer
- KEBS ISO 9001:2015 certification badge
- "TWWDA IS ISO9001:2015 CERTIFIED"

---

## 7. EMAIL NOTIFICATIONS

### 7.1 Email Triggers
| Event | Recipients | Template |
|-------|-----------|----------|
| Internal notice submitted | CEO, Secretary to CEO | notice_submitted |
| Internal notice approved | CS or DD IA (creator) | notice_approved |
| Internal notice rejected | CS or DD IA (creator) | notice_rejected |
| Officer assigned for papers | DD (+ Director if exists) | officer_assigned |
| Board paper submitted (L1) | Director | paper_submitted_l1 |
| Board paper submitted (CEO) | CEO, Secretary to CEO | paper_submitted_ceo |
| Board paper approved (Director) | DD, CEO | paper_approved_l1 |
| Board paper rejected (Director) | DD | paper_rejected_l1 |
| Board paper approved (CEO) | Director, DD, CS | paper_approved_ceo |
| Board paper rejected (CEO) | Director or DD (chain) | paper_rejected_ceo |
| Board notice submitted | CEO, Secretary to CEO | board_notice_submitted |
| Board notice approved | CS or DD IA | board_notice_approved |
| 21-day meeting notice | Board committee members | official_notice_21day |
| 7-day meeting reminder | Board committee members | official_notice_7day |
| OTP for conflict declaration | Board member | conflict_otp |

### 7.2 Key Email Addresses
- CEO: pgichuki@tanawwda.go.ke
- Secretary to CEO: ceo@tanawwda.go.ke

---

## 8. FILE STRUCTURE (NEW)

```
app/
  Http/
    Controllers/
      Twwda/
        MeetingWorkflowController.php      # Stage 1: Initiation
        InternalNoticeController.php       # Stage 2-3: Notice CRUD + approval
        OfficerAssignmentController.php    # Stage 4: Assign officers
        BoardPaperWorkflowController.php   # Stage 5: Paper preparation
        DirectorReviewController.php       # Stage 6: Director review
        CeoPaperReviewController.php       # Stage 7: CEO review
        MeetingPreparationController.php   # Stage 8: Arrange papers
        LiveMeetingController.php          # Stage 9: Meeting day
        BoardNoticeWorkflowController.php  # Board member notifications
        TwwdaDashboardController.php       # Analytics dashboards
    Services/
      Twwda/
        MeetingWorkflowService.php
        InternalNoticeService.php
        OfficerAssignmentService.php
        BoardPaperWorkflowService.php
        DirectorReviewService.php
        CeoPaperReviewService.php
        MeetingPreparationService.php
        LiveMeetingService.php
        BoardNoticeService.php
        TwwdaNotificationService.php
        TwwdaAuditService.php
        TwwdaEmailService.php
        PdfGeneratorService.php

  Models/
    Twwda/
      TwwdaMeeting.php
      TwwdaInternalNotice.php
      TwwdaInternalNoticeComment.php
      TwwdaOfficerAssignment.php
      TwwdaBoardPaper.php
      TwwdaBoardPaperAnnexure.php
      TwwdaBoardPaperComment.php
      TwwdaStickyNote.php
      TwwdaMeetingPreparation.php
      TwwdaMeetingAttendance.php
      TwwdaConflictDeclaration.php
      TwwdaBoardNotice.php
      TwwdaBoardNoticeComment.php
      TwwdaAuditLog.php
      TwwdaNotification.php
      TwwdaUserDirectorateMapping.php

database/
  migrations/
    2026_05_26_000001_create_twwda_user_directorate_mappings_table.php
    2026_05_26_000002_create_twwda_meetings_table.php
    2026_05_26_000003_create_twwda_internal_notices_table.php
    2026_05_26_000004_create_twwda_internal_notice_comments_table.php
    2026_05_26_000005_create_twwda_officer_assignments_table.php
    2026_05_26_000006_create_twwda_board_papers_table.php
    2026_05_26_000007_create_twwda_board_paper_annexures_table.php
    2026_05_26_000008_create_twwda_board_paper_comments_table.php
    2026_05_26_000009_create_twwda_sticky_notes_table.php
    2026_05_26_000010_create_twwda_meeting_preparations_table.php
    2026_05_26_000011_create_twwda_meeting_attendance_table.php
    2026_05_26_000012_create_twwda_conflict_declarations_table.php
    2026_05_26_000013_create_twwda_board_notices_table.php
    2026_05_26_000014_create_twwda_board_notice_comments_table.php
    2026_05_26_000015_create_twwda_audit_logs_table.php
    2026_05_26_000016_create_twwda_notifications_table.php

resources/
  views/
    twwda/
      dashboard/
        index.blade.php                    # Main workflow dashboard
      meetings/
        almanac.blade.php                  # Stage 1: Meeting initiation
        show.blade.php                     # Meeting detail/timeline
      internal-notices/
        create.blade.php                   # Stage 2: Create notice
        edit.blade.php                     # Edit/resubmit notice
        show.blade.php                     # View notice (official format)
        ceo-review.blade.php              # Stage 3: CEO review
        ceo-pending.blade.php             # CEO pending notices
      assignments/
        index.blade.php                    # Stage 4: Assign officers
        dashboard.blade.php               # Assignment status dashboard
      papers/
        create.blade.php                   # Stage 5: Create paper
        edit.blade.php                     # Edit/resubmit paper
        show.blade.php                     # View paper
        director-review.blade.php         # Stage 6: Director review
        ceo-review.blade.php              # Stage 7: CEO review with sticky notes
      preparations/
        index.blade.php                    # Stage 8: Arrange papers
      live-meeting/
        index.blade.php                    # Stage 9: Meeting day
        attendance.blade.php              # Attendance register
        conflict.blade.php               # Conflict declaration form
        otp-verify.blade.php             # OTP verification
      board-notices/
        create.blade.php                   # Official board notice creation
        preview.blade.php                 # Notice preview with letterhead
        ceo-review.blade.php             # CEO approval
      components/
        workflow-progress.blade.php       # Reusable workflow progress bar
        letterhead.blade.php              # TWWDA letterhead component
        signature-pad.blade.php           # Stylus signature component
        sticky-notes.blade.php            # PDF sticky notes component
        countdown-timer.blade.php         # Meeting countdown
      emails/
        notice-submitted.blade.php
        notice-approved.blade.php
        notice-rejected.blade.php
        officer-assigned.blade.php
        paper-submitted.blade.php
        paper-approved.blade.php
        paper-rejected.blade.php
        board-notice-submitted.blade.php
        board-notice-approved.blade.php
        official-notice.blade.php
        conflict-otp.blade.php

routes/
  twwda.php                               # All Version 2 routes
```

---

## 9. ROUTE DEFINITIONS

```
Route prefix: /twwda

# Stage 1: Meeting Initiation
GET    /twwda/meetings/almanac                    # Dashboard with almanac schedules
POST   /twwda/meetings/{schedule}/initiate        # Initiate meeting from schedule

# Stage 2-3: Internal Notices
GET    /twwda/meetings/{meeting}/internal-notice          # Create/view notice
POST   /twwda/meetings/{meeting}/internal-notice          # Store notice
PUT    /twwda/internal-notices/{notice}                    # Update notice
POST   /twwda/internal-notices/{notice}/submit             # Submit to CEO
GET    /twwda/ceo/internal-notices                         # CEO pending notices
GET    /twwda/ceo/internal-notices/{notice}                # CEO review page
POST   /twwda/ceo/internal-notices/{notice}/decision       # Approve/reject
POST   /twwda/internal-notices/{notice}/special-approve    # Special approval

# Stage 4: Officer Assignments
GET    /twwda/meetings/{meeting}/assign-officers           # Assignment page
POST   /twwda/meetings/{meeting}/assign-officers           # Store assignments
POST   /twwda/meetings/{meeting}/assign-officers/add       # Add more officers
GET    /twwda/meetings/{meeting}/assignment-dashboard      # Status dashboard

# Stage 5: Board Paper Preparation
GET    /twwda/meetings/{meeting}/board-paper               # Paper creation
POST   /twwda/meetings/{meeting}/board-paper               # Store paper
PUT    /twwda/board-papers/{paper}                         # Update paper
POST   /twwda/board-papers/{paper}/submit                  # Submit paper
POST   /twwda/board-papers/{paper}/annexures               # Upload annexure
DELETE /twwda/board-papers/{paper}/annexures/{annexure}     # Remove annexure

# Stage 6: Director Review
GET    /twwda/director/board-papers                        # Director's pending papers
GET    /twwda/director/board-papers/{paper}                # Review page
POST   /twwda/director/board-papers/{paper}/decision       # Approve/reject

# Stage 7: CEO Paper Review
GET    /twwda/ceo/meeting-papers/{meeting}                 # CEO review meeting papers
GET    /twwda/ceo/meeting-papers/{paper}/review            # Paper review with PDF
POST   /twwda/ceo/meeting-papers/{paper}/decision          # Approve/reject
POST   /twwda/ceo/meeting-papers/{paper}/sticky-notes      # Add sticky notes
DELETE /twwda/ceo/sticky-notes/{note}                      # Remove sticky note

# Stage 8: Meeting Preparation
GET    /twwda/meetings/{meeting}/prepare                   # Preparation dashboard
POST   /twwda/meetings/{meeting}/prepare/reorder           # Reorder papers
POST   /twwda/meetings/{meeting}/prepare/add-item          # Add agenda item
POST   /twwda/meetings/{meeting}/prepare/remove-paper      # Remove paper
POST   /twwda/meetings/{meeting}/prepare/finalize          # Finalize

# Stage 9: Live Meeting
POST   /twwda/meetings/{meeting}/start                     # Start meeting
GET    /twwda/meetings/{meeting}/live                      # Live meeting page
POST   /twwda/meetings/{meeting}/attendance                # Record attendance
GET    /twwda/meetings/{meeting}/conflict                  # Conflict form
POST   /twwda/meetings/{meeting}/conflict                  # Submit declaration
POST   /twwda/meetings/{meeting}/conflict/verify-otp       # Verify OTP
POST   /twwda/meetings/{meeting}/conflict/sign             # Sign declaration

# Board Member Notifications
GET    /twwda/meetings/{meeting}/board-notice              # Create board notice
POST   /twwda/meetings/{meeting}/board-notice              # Store board notice
POST   /twwda/board-notices/{notice}/submit                # Submit to CEO
GET    /twwda/ceo/board-notices                            # CEO review
POST   /twwda/ceo/board-notices/{notice}/decision          # Approve/reject + sign
POST   /twwda/board-notices/{notice}/send                  # Send to board members

# Dashboard & Timeline
GET    /twwda/dashboard                                    # Main dashboard
GET    /twwda/meetings/{meeting}/timeline                  # Full workflow timeline

# Users Dashboard
GET    /twwda/users                                        # Enhanced users with analytics
```

---

## 10. IMPLEMENTATION PRIORITIES

### Phase 1: Foundation (Database + Models + Base UI)
1. Create all 16 migration files
2. Create all 16 Eloquent models
3. Create TwwdaAuditService and TwwdaNotificationService
4. Create workflow progress bar component
5. Add twwda.php route file
6. User-directorate mapping functionality

### Phase 2: Stages 1-3 (Initiation + Internal Notice)
7. Meeting initiation from almanac (Stage 1)
8. Internal notice creation (Stage 2)
9. CEO approval workflow with comments (Stage 3)
10. Email notifications for Stages 1-3
11. Official notice display with letterhead

### Phase 3: Stages 4-5 (Assignment + Paper Prep)
12. Officer assignment with agenda items (Stage 4)
13. Read receipts and dashboard (Stage 4)
14. Board paper template and creation (Stage 5)
15. Annexure uploads (Stage 5)

### Phase 4: Stages 6-7 (Approvals)
16. Director review workflow (Stage 6)
17. CEO review with PDF sticky notes (Stage 7)
18. Stylus signature on approval (Stage 7)

### Phase 5: Stages 8-9 (Prep + Meeting Day)
19. Paper arrangement and ordering (Stage 8)
20. Meeting day activation (Stage 9)
21. Conflict of interest with OTP + signature (Stage 9)
22. Attendance register (Stage 9)

### Phase 6: Board Member Notifications
23. Official board notice creation with template
24. CEO approval of board notices
25. 21-day and 7-day automated notices
26. Meeting date amendment workflow

### Phase 7: Dashboards + Polish
27. Users dashboard with analytics/visualizations
28. Workflow timeline view
29. Meeting countdown timers
30. Full audit log integration

---

## 11. TECHNICAL DEPENDENCIES

### JavaScript Libraries (Already Available)
- jQuery DataTables (with Bootstrap 5 buttons, responsive, select)
- SweetAlert2 (confirmations)
- Toastr (notifications)
- ApexCharts (analytics dashboards)
- Quill Editor (rich text)

### New Libraries Needed
- **Signature Pad** (szimek/signature_pad) - Stylus/touch signatures
- **PDF.js** (mozilla/pdfjs-dist) - PDF rendering for sticky notes
- **Sortable.js** (SortableJS/Sortable) - Drag-and-drop paper reordering
- **DomPDF** (barryvdh/laravel-dompdf) - Already likely installed for PDF generation

### Laravel Packages
- spatie/laravel-permission (existing)
- barryvdh/laravel-dompdf (for PDF generation with letterhead)
- maatwebsite/excel (if needed for exports)

---

## 12. BRANDING ASSETS

### Letterhead Components
- **Logo**: TWWDA circular logo (water-themed, green/blue)
- **Header Image**: Full-width header with logo + water imagery + address block
  - Located at: `public/backend/assets/images/twwda-letterhead.png`
- **Footer Badge**: KEBS ISO 9001:2015 certification
  - Located at: `public/backend/assets/images/twwda-kebs-badge.png`
- **Brand Colors**: Navy #005bac, Green #099139

### CEO Signatory
- Name: Eng. Philip Gichuki, MBS
- Title: CHIEF EXECUTIVE OFFICER
