# Manager Approval Flow - Implementation Summary

## Overview
Implementasi workflow approval planning dengan fitur revision tracking dan version control untuk V&B Activity Planning System.

## Architecture

### Database Schema

#### 1. `vnb_plan_revisions` (Tracking Revisions)
```
- id: Primary Key
- vnb_plan_id: Foreign Key ke vnb_plans
- revision_number: Nomor revisi ke berapa (1, 2, 3, ...)
- requested_by: Manager ID yang request revisi
- revision_notes: Catatan revisi dari manager
- status: pending|in_progress|submitted|applied
  - pending: Draft revisi belum dikerjakan
  - in_progress: New hire sedang mengerjakan revisi
  - submitted: New hire sudah kirim perubahan revisi
  - applied: Manager approve revisi tersebut
- requested_at: Timestamp saat manager request
- submitted_at: Timestamp saat new hire submit
- applied_at: Timestamp saat manager approve
```

#### 2. `vnb_plan_revision_details` (Version Control)
```
- id: Primary Key
- vnb_plan_revision_id: FK ke vnb_plan_revisions
- vnb_plan_item_id: FK ke vnb_plan_items (activity yang direvisi)
- old_values: JSON - nilai sebelum revisi
  Struktur: {
    "activity_title": "...",
    "description": "...",
    "implementation_date": "...",
    "deliverables": "...",
    "behavior_metrics": "..."
  }
- new_values: JSON - nilai setelah revisi (struktur sama)
- changed_by: Employee ID (new hire yang melakukan perubahan)
- created_at: Timestamp
```

#### 3. `vnb_plans` (Updated)
```
- Tambahan fields:
  - revision_count: Integer - berapa banyak revisi diminta
  - revision_notes: Text - catatan revisi terakhir dari manager

- Tambahan statuses:
  - revision_requested: Manager sudah request revisi
  - revision_draft: New hire sedang dalam draft revisi
```

## Workflow

### FASE 1: Manager Request Revision

**Trigger**: Manager lihat planning di approval list dan click "Request Revisi"

**Flow**:
1. Manager di `/manager/approval-requests` → click planning
2. Buka detail view → `/manager/approval/{planId}`
3. Click button "Request Revisi" (merah)
4. Modal terbuka → input catatan revisi
5. Submit → API call

**API Call**:
```
POST /api/manager/plans/{planId}/request-revision
Body: {
  "revision_notes": "Sesuaikan tanggal implementasi untuk activity marketing campaign..."
}
```

**Backend Actions**:
- Create record di `vnb_plan_revisions` dengan status `pending`
- Update `vnb_plans` status menjadi `revision_requested`
- Increment `revision_count`
- Create activity log entry
- Send notification ke new hire (optional)

**Result**: 
- Plan status: `revision_requested`
- Revision status: `pending` (draft)
- New hire bisa lihat di `/vnb-plans/pending-revisions`

---

### FASE 2: New Hire Edit & Submit Revision

**Trigger**: New hire lihat notifikasi atau akses `/vnb-plans/pending-revisions`

**Flow**:
1. New hire lihat list pending revisions
2. Click "Edit Aktivitas" pada revisi yang ingin dikerjakan
3. Modal editor terbuka dengan:
   - Daftar aktivitas yang need revision (berdasarkan revision details)
   - Form fields untuk setiap activity (title, description, dates, deliverables, metrics)
4. New hire edit fields apa yang manager minta
5. Click "Simpan Perubahan"

**API Call**:
```
POST /api/vnb-plans/{planId}/submit-revision/{revisionId}
Body: {
  "changes": [
    {
      "item_id": 123,
      "old_values": {
        "activity_title": "Old Title",
        "description": "Old desc",
        ...
      },
      "new_values": {
        "activity_title": "New Title",
        "description": "New desc",
        ...
      }
    },
    ...
  ]
}
```

**Backend Actions**:
- Update `vnb_plan_items` dengan new values
- Create `vnb_plan_revision_details` records (version control) untuk setiap change
- Update revision status dari `pending` → `submitted`
- Update plan status dari `revision_requested` → `revision_draft` (temporary status)
- Create activity log dengan detail perubahan

**Result**:
- Activities sudah updated
- Revision status: `submitted`
- Plan status: `revision_draft`
- Manager bisa lihat changes di revision history

---

### FASE 3: Manager Review & Approve

**Trigger**: Manager kembali ke approval list atau notification

**Flow**:
1. Manager lihat planning dengan status `revision_draft`
2. Click detail untuk lihat perubahan
3. Modal "Lihat Revision History" → view all changes dari old to new values
4. Jika puas: Click "Approve Planning"
5. Jika tidak puas: Click "Request Revisi Lagi"

**Approve Action**:
```
POST /api/manager/plans/{planId}/approve
```

**Backend Actions** (Approve):
- Update plan status: `revision_draft` → `approved`
- Update revision status: `submitted` → `applied`
- Set `approved_by` dan `approved_at`
- Mark all pending revisions as applied

**Backend Actions** (Request Revisi Lagi):
- Create new revision record (increment revision_number)
- Update plan status: `revision_draft` → `revision_requested`
- New revision status: `pending`

---

## API Endpoints

### Manager Endpoints

#### 1. Request Revision
```
POST /api/manager/plans/{planId}/request-revision
Authorization: Bearer {token}

Body:
{
  "revision_notes": "Catatan revisi dari manager"
}

Response:
{
  "success": true,
  "message": "Permintaan revisi berhasil dikirim ke new hire",
  "data": {
    "revision_id": 1,
    "revision_number": 1,
    "status": "pending"
  }
}
```

#### 2. Approve Plan
```
POST /api/manager/plans/{planId}/approve
Authorization: Bearer {token}

Response:
{
  "success": true,
  "message": "Planning berhasil diapprove",
  "data": {
    "plan_id": 1,
    "status": "approved",
    "approved_at": "2024-04-01 10:30:00"
  }
}
```

#### 3. Get Revision History
```
GET /api/manager/plans/{planId}/revisions/history
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": {
    "plan_id": 1,
    "plan_title": "Planning Title",
    "total_revisions": 2,
    "revisions": [
      {
        "id": 1,
        "revision_number": 1,
        "status": "applied",
        "status_label": "Diterapkan",
        "revision_notes": "Catatan dari manager",
        "requested_by": "Manager Name",
        "requested_at": "2024-04-01 09:00:00",
        "submitted_at": "2024-04-01 09:30:00",
        "applied_at": "2024-04-01 10:00:00",
        "activities_changed": 2,
        "details": [
          {
            "activity_id": 123,
            "activity_title": "Marketing Campaign",
            "changed_fields": {
              "implementation_date": {
                "old": "2024-05-01",
                "new": "2024-05-15"
              },
              "deliverables": {
                "old": "Plan doc",
                "new": "Plan doc + Presentation"
              }
            },
            "changed_by": "New Hire Name",
            "changed_at": "2024-04-01 09:30:00"
          }
        ]
      }
    ]
  }
}
```

---

### New Hire Endpoints

#### 1. Get Pending Revisions
```
GET /api/new-hire/pending-revisions
Authorization: Bearer {token}

Response:
{
  "success": true,
  "data": [
    {
      "id": 1,
      "plan_id": 10,
      "plan_title": "Phase 1 Planning",
      "plan_phase": 1,
      "revision_number": 1,
      "status": "pending",
      "status_label": "Draft Revisi",
      "revision_notes": "Sesuaikan tanggal implementasi...",
      "requested_by": "Manager Name",
      "requested_at": "2024-04-01 09:00:00",
      "items_to_revise": 2,
      "details": [
        {
          "activity_id": 123,
          "activity_title": "Marketing Campaign"
        }
      ]
    }
  ]
}
```

#### 2. Submit Revision Changes
```
POST /api/vnb-plans/{planId}/submit-revision/{revisionId}
Authorization: Bearer {token}

Body:
{
  "changes": [
    {
      "item_id": 123,
      "old_values": {
        "activity_title": "Old Title",
        "description": "Old description",
        "implementation_date": "2024-05-01",
        "deliverables": "Plan doc",
        "behavior_metrics": ["Metric 1"]
      },
      "new_values": {
        "activity_title": "New Title",
        "description": "New description",
        "implementation_date": "2024-05-15",
        "deliverables": "Plan doc + Presentation",
        "behavior_metrics": ["Metric 1", "Metric 2"]
      }
    }
  ]
}

Response:
{
  "success": true,
  "message": "Perubahan revisi berhasil disimpan. Menunggu approval manager.",
  "data": {
    "revision_id": 1,
    "status": "submitted"
  }
}
```

---

## Views

### Manager Views

#### 1. Approval Requests List
**Route**: `/manager/approval-requests`
- Table dengan filter Jenis (Planning/Activity), status, employee
- Column: Jenis, New Hire, NIP, Perusahaan, Judul, Phase, Waktu Submit
- Action: Click row → buka detail

#### 2. Approval Detail
**Route**: `/manager/approval/{planId}`
- Header: Plan title, employee info, status badge
- Info card: Phase, Status, Total Aktivitas
- Section: Plan description
- Section: Activity planning list dengan status
- Section: Revision history (jika ada revisions)
- Action buttons: 
  - "Approve Planning" (hijau) - jika ready
  - "Request Revisi" (merah) - untuk minta revisi lagi

**Modals**:
- Approve confirmation
- Request revision form
- Revision history detail with version control

---

### New Hire Views

#### 1. Pending Revisions List
**Route**: `/vnb-plans/pending-revisions`
- Alert: "Tidak ada revisi pending" atau list revisions
- Card untuk setiap revision dengan:
  - Title & phase
  - Revision number & status badge
  - Catatan revisi dari manager
  - List activities yang perlu direvisi
  - Buttons: "Edit Aktivitas", "Lihat History"

#### 2. Revision Editor Modal
- Header: Plan title & revision number
- Section: Catatan revisi dari manager (read-only, highlighted)
- Editor fields untuk setiap activity:
  - Judul Aktivitas (text)
  - Deskripsi (textarea)
  - Tanggal Implementasi (date)
  - Deliverables (text)
  - Metrics Perilaku (textarea)
- Info: "Perubahan akan dicatat dalam Version Control"
- Buttons: "Batal", "Simpan Perubahan"

---

## Key Features

### 1. Version Control
- Setiap perubahan activity terdokumentasi dalam `vnb_plan_revision_details`
- Menyimpan old_values dan new_values untuk audit trail
- Accessible oleh manager dan new hire
- Tampil dalam revision history modal

### 2. Status Tracking
- Plan status transitions tercatat dengan jelas
- Revision status tracking (pending → in_progress → submitted → applied)
- Activity logs untuk setiap major action

### 3. Notifications (Optional Future)
- Notif ke new hire saat manager request revisi
- Notif ke manager saat new hire submit revisi
- Badge counter di sidebar

### 4. Authorization
- Manager hanya bisa approve/revise planning untuk new hire yang di-manage
- New hire hanya bisa edit revisions untuk planning milik mereka sendiri

---

## Implementation Checklist

- [x] Database migration (vnb_plan_revisions, revision_details)
- [x] Models (VnbPlanRevision, VnbPlanRevisionDetail)
- [x] Manager API endpoints (request-revision, approve, get-history)
- [x] New hire API endpoints (pending-revisions, submit-revision)
- [x] Manager approval detail view
- [x] Manager revision history modal
- [x] New hire pending revisions view
- [x] New hire revision editor modal
- [x] Navigation links
- [ ] Email notifications (optional)
- [ ] Dashboard badges for pending items (partial - added to sidebar)
- [ ] Activity log integration (done in backend)

---

## Testing Scenarios

### Scenario 1: Happy Path
1. Manager request revisi dengan catatan
2. New hire lihat revisi, edit activities
3. New hire submit changes
4. Manager lihat version control history
5. Manager approve

### Scenario 2: Multiple Revisions
1. Manager request revisi #1
2. New hire submit changes
3. Manager request revisi #2 (again)
4. New hire submit changes #2
5. Manager approve

### Scenario 3: View History
- Manager bisa lihat all previous revisions dengan full version control details
- Bisa compare old vs new values untuk setiap activity

---

## Notes

- Revision hanya untuk planning stage (bukan activity execution)
- Once approved, planning tidak bisa di-revise lagi (kecuali ada revisi baru dari manager)
- Version control menyimpan semua perubahan untuk audit purposes
- Timestamps documented untuk full traceability
