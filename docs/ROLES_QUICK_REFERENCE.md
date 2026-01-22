# 🎯 Role Quick Reference

Panduan cepat untuk memahami role dan permission di sistem Kos Management.

---

## 3 Role Utama

### 1️⃣ **ADMIN** - Full Control

```
├── 👥 Penyewa (Tenant)
│   ├── ✅ View, Create, Update (All), Delete, Restore
│   └── ✅ Akses data sensitif (email, phone, status)
├── 💰 Pembayaran (Payment)
│   ├── ✅ View, Create, Update, Delete
│   ├── ✅ Confirm & Reject pembayaran
│   └── ✅ Akses semua status
├── 📊 Biaya Operasional
│   ├── ✅ View, Create, Update, Delete
│   ├── ✅ Approve & Reject
│   └── ✅ Kelola semua biaya
└── 📈 Laporan Keuangan
    ├── ✅ View, Create, Update, Delete
    ├── ✅ Publish & Archive
    └── ✅ Full control laporan
```

### 2️⃣ **ACCOUNTANT** - Financial Management

```
├── 👥 Penyewa (Tenant)
│   ├── ✅ View List & Detail
│   └── ❌ Create, Update, Delete
├── 💰 Pembayaran (Payment)
│   ├── ✅ View List & Detail
│   ├── ✅ Confirm pembayaran
│   └── ❌ Create, Update, Delete
├── 📊 Biaya Operasional
│   ├── ✅ View & Create
│   ├── ✅ Update (Hanya milik sendiri, status=recorded)
│   └── ❌ Delete, Approve, Reject
└── 📈 Laporan Keuangan
    ├── ✅ View & Create
    └── ❌ Update, Publish, Delete
```

### 3️⃣ **STAFF** - Daily Operations

```
├── 👥 Penyewa (Tenant)
│   ├── ✅ View & Create
│   ├── ⚠️ Update (Hanya basic data, TIDAK sensitif)
│   └── ❌ Delete
├── 💰 Pembayaran (Payment)
│   ├── ✅ View & Create
│   └── ❌ Update, Confirm, Delete
├── 📊 Biaya Operasional
│   └── ❌ No Access (Lihat: Accountant only)
└── 📈 Laporan Keuangan
    └── ❌ No Access (Lihat: Accountant & Admin only)
```

---

## Permission Matrix (Single View)

|                | TENANT | PAYMENT | OP.COST | REPORT |
| -------------- | ------ | ------- | ------- | ------ |
| **ADMIN**      | ✅✅✅ | ✅✅✅  | ✅✅✅  | ✅✅✅ |
| **ACCOUNTANT** | 👁️     | ✅      | ✅      | ✅     |
| **STAFF**      | ✅⚠️   | ✅      | ❌      | ❌     |

Legend: ✅ = Full Access | ⚠️ = Limited | 👁️ = View Only | ❌ = No Access | ✅✅✅ = Admin-only actions

---

## Data Sensitive vs Non-Sensitive

### Data Sensitif (Admin Only Edit)

```
❌ Email penyewa
❌ Nomor telepon
❌ Status penyewa
❌ Data identitas
❌ Financial data detail
```

### Data Non-Sensitif (Staff Bisa Edit)

```
✅ Nama penyewa
✅ Alamat
✅ Informasi kontak umum
✅ Catatan penyewa
✅ Room assignment
```

---

## Role by Department

| Department                     | Role       | Responsibility                           |
| ------------------------------ | ---------- | ---------------------------------------- |
| **Finance/Accounting**         | Accountant | Terima & catat pembayaran, buat laporan  |
| **Administration**             | Admin      | Oversee semua, approve keputusan penting |
| **Reception/Customer Service** | Staff      | Daftar tenant, handle pertanyaan         |

---

## How to Check Role in Code

### Laravel Authorization

```php
// Method 1: Trait methods
$user->isAdmin();      // boolean
$user->isAccountant(); // boolean
$user->isStaff();      // boolean

// Method 2: hasRole
$user->hasRole('admin');           // single
$user->hasRole(['admin', 'staff']); // multiple

// Method 3: Policy authorization
$this->authorize('update', $tenant);
```

### Blade Template

```blade
@can('update', $tenant)
    Show edit button
@endcan

@if($user->isAdmin())
    Admin-only content
@endif
```

---

## Common Actions by Role

### Admin Checklist

- [ ] Approve pembayaran pending
- [ ] Approve pengeluaran biaya
- [ ] Publish laporan keuangan
- [ ] Manage tenant accounts
- [ ] Assign roles to staff

### Accountant Checklist

- [ ] Record pembayaran masuk
- [ ] Catat pengeluaran operasional
- [ ] Monitor cash flow
- [ ] Generate laporan bulanan
- [ ] Review pembayaran overdue

### Staff Checklist

- [ ] Register tenant baru
- [ ] Update informasi tenant
- [ ] Log pembayaran masuk
- [ ] Follow-up pembayaran
- [ ] Handle tenant complaints

---

**📖 Full Documentation:** See `ROLES_AND_PERMISSIONS.md` for detailed information
