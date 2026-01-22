# 📋 Sistem Role & Permissions

Dokumentasi lengkap tentang role (peran) yang tersedia dalam sistem Kos Management dan apa saja yang bisa dilakukan setiap role.

---

## 📌 Overview Role

Sistem ini memiliki **3 role utama** dengan tingkat akses dan permission yang berbeda:

| Role           | Level            | Deskripsi                                   |
| -------------- | ---------------- | ------------------------------------------- |
| **Admin**      | ⭐⭐⭐ Tertinggi | Akses penuh ke semua fitur dan data sistem  |
| **Accountant** | ⭐⭐ Menengah    | Fokus pada pengelolaan keuangan dan laporan |
| **Staff**      | ⭐ Dasar         | Pengelolaan operasional sehari-hari         |

---

## 🔐 Role: ADMIN

**Tingkat Akses:** Tertinggi | **Akses Penuh Sistem**

### Deskripsi Umum

Admin memiliki kontrol penuh atas sistem. Role ini bertanggung jawab untuk manajemen user, konfigurasi sistem, dan approval untuk operasi-operasi penting.

### Permissions Detail

#### 👥 Manajemen Penyewa (Tenant)

| Aksi                  | Permission  | Catatan                                                |
| --------------------- | ----------- | ------------------------------------------------------ |
| ✅ **View List**      | viewAny     | Melihat daftar semua penyewa                           |
| ✅ **View Detail**    | view        | Melihat detail penyewa tertentu                        |
| ✅ **Tambah Penyewa** | create      | Menambah penyewa baru                                  |
| ✅ **Edit Penyewa**   | update      | Edit semua data penyewa termasuk data sensitif         |
| ✅ **Hapus Penyewa**  | delete      | Menghapus penyewa dari sistem                          |
| ✅ **Restore**        | restore     | Mengembalikan penyewa yang sudah dihapus (soft delete) |
| ✅ **Force Delete**   | forceDelete | Menghapus permanent dari database                      |

**Akses Data Sensitif:**

- Email
- Nomor Telepon
- Status Penyewa
- Data Identitas

#### 💰 Manajemen Pembayaran (Payment)

| Aksi                         | Permission | Catatan                            |
| ---------------------------- | ---------- | ---------------------------------- |
| ✅ **View List**             | viewAny    | Melihat semua transaksi pembayaran |
| ✅ **View Detail**           | view       | Melihat detail pembayaran          |
| ✅ **Tambah Pembayaran**     | create     | Membuat pembayaran baru            |
| ✅ **Edit Pembayaran**       | update     | Mengubah data pembayaran           |
| ✅ **Konfirmasi Pembayaran** | confirm    | Mengkonfirmasi pembayaran pending  |
| ✅ **Tolak Pembayaran**      | reject     | Menolak pembayaran pending         |
| ✅ **Hapus Pembayaran**      | delete     | Menghapus transaksi pembayaran     |

**Status yang Bisa Dikelola:**

- Pending
- Confirmed
- Overdue
- Failed

#### 📊 Manajemen Biaya Operasional (Operational Cost)

| Aksi                 | Permission | Catatan                             |
| -------------------- | ---------- | ----------------------------------- |
| ✅ **View List**     | viewAny    | Melihat semua biaya operasional     |
| ✅ **View Detail**   | view       | Melihat detail biaya                |
| ✅ **Tambah Biaya**  | create     | Membuat catatan biaya baru          |
| ✅ **Edit Biaya**    | update     | Mengubah biaya apapun               |
| ✅ **Approve Biaya** | approve    | Menyetujui biaya yang sudah dicatat |
| ✅ **Reject Biaya**  | reject     | Menolak biaya yang belum disetujui  |
| ✅ **Hapus Biaya**   | delete     | Menghapus catatan biaya             |

**Proses Approval:**

- Recorded → Pending Approval
- Approved → Tercatat dalam laporan

#### 📈 Manajemen Laporan Keuangan (Financial Report)

| Aksi                   | Permission | Catatan                                |
| ---------------------- | ---------- | -------------------------------------- |
| ✅ **View List**       | viewAny    | Melihat semua laporan keuangan         |
| ✅ **View Detail**     | view       | Melihat detail laporan                 |
| ✅ **Buat Laporan**    | create     | Membuat laporan keuangan baru          |
| ✅ **Edit Laporan**    | update     | Edit laporan dalam status draft        |
| ✅ **Publish Laporan** | publish    | Mempublikasikan laporan dari draft     |
| ✅ **Archive Laporan** | archive    | Mengarsip laporan yang sudah published |
| ✅ **Hapus Laporan**   | delete     | Menghapus laporan                      |

**Workflow Laporan:**

- Draft → Edit/Update
- Publish → Locked (read-only)
- Archive → Historical data

#### 👤 Manajemen User & Role

- Mengelola akun user lain
- Mengubah role user (Admin, Accountant, Staff)
- Suspend/activate user
- Reset password user

#### ⚙️ Konfigurasi Sistem

- Pengaturan umum sistem
- Backup & restore data
- Audit log
- View semua activity log

---

## 💼 Role: ACCOUNTANT

**Tingkat Akses:** Menengah | **Fokus Keuangan**

### Deskripsi Umum

Accountant menangani semua aspek keuangan perusahaan. Role ini hanya bisa mengakses data yang berkaitan dengan pembayaran dan laporan keuangan.

### Permissions Detail

#### 👥 Manajemen Penyewa (Tenant)

| Aksi               | Permission | Status                           |
| ------------------ | ---------- | -------------------------------- |
| ✅ **View List**   | viewAny    | Bisa melihat daftar penyewa      |
| ✅ **View Detail** | view       | Bisa melihat detail penyewa      |
| ❌ **Create**      | create     | ❌ Tidak boleh menambah penyewa  |
| ❌ **Update**      | update     | ❌ Tidak boleh edit data penyewa |
| ❌ **Delete**      | delete     | ❌ Tidak boleh hapus penyewa     |

**Akses Terbatas:** Hanya bisa melihat data penyewa yang relevan dengan pembayaran

#### 💰 Manajemen Pembayaran (Payment)

| Aksi                   | Permission | Catatan                                       |
| ---------------------- | ---------- | --------------------------------------------- |
| ✅ **View List**       | viewAny    | Melihat semua pembayaran                      |
| ✅ **View Detail**     | view       | Melihat detail pembayaran                     |
| ❌ **Create**          | create     | ❌ Tidak boleh buat pembayaran baru           |
| ✅ **Edit Pembayaran** | update     | Update pembayaran status: Pending & Confirmed |
| ✅ **Konfirmasi**      | confirm    | Mengkonfirmasi pembayaran pending             |
| ❌ **Reject**          | reject     | ❌ Hanya admin yang bisa reject               |
| ❌ **Delete**          | delete     | ❌ Tidak boleh hapus pembayaran               |

**Batasan Edit:**

- Hanya untuk status: Pending dan Confirmed
- Tidak bisa mengubah jumlah pembayaran

#### 📊 Manajemen Biaya Operasional (Operational Cost)

| Aksi                | Permission | Catatan                                                   |
| ------------------- | ---------- | --------------------------------------------------------- |
| ✅ **View List**    | viewAny    | Melihat semua biaya operasional                           |
| ✅ **View Detail**  | view       | Melihat detail biaya                                      |
| ✅ **Tambah Biaya** | create     | Membuat catatan biaya baru                                |
| ✅ **Edit Biaya**   | update     | Hanya untuk biaya yang dibuat sendiri & status "recorded" |
| ❌ **Approve**      | approve    | ❌ Hanya admin yang bisa approve                          |
| ❌ **Reject**       | reject     | ❌ Hanya admin yang bisa reject                           |
| ❌ **Delete**       | delete     | ❌ Tidak boleh hapus biaya                                |

**Batasan Update:**

- Hanya biaya yang dibuat oleh accountant tersebut
- Status harus "recorded"
- Tidak bisa ubah tanggal

#### 📈 Manajemen Laporan Keuangan (Financial Report)

| Aksi                | Permission | Catatan                          |
| ------------------- | ---------- | -------------------------------- |
| ✅ **View List**    | viewAny    | Melihat semua laporan            |
| ✅ **View Detail**  | view       | Melihat detail laporan           |
| ✅ **Buat Laporan** | create     | Membuat laporan keuangan baru    |
| ❌ **Edit**         | update     | ❌ Hanya admin yang bisa edit    |
| ❌ **Publish**      | publish    | ❌ Hanya admin yang bisa publish |
| ❌ **Archive**      | archive    | ❌ Hanya admin yang bisa archive |
| ❌ **Delete**       | delete     | ❌ Tidak boleh hapus laporan     |

**Akses:**

- Bisa membuat draft laporan
- Tidak bisa publish (admin yang publish)
- Full visibility ke semua laporan

---

## 👔 Role: STAFF

**Tingkat Akses:** Dasar | **Operasional Harian**

### Deskripsi Umum

Staff mengelola operasional sehari-hari seperti pendaftaran penyewa dan pencatatan pembayaran. Role ini tidak memiliki akses ke data finansial detail dan approval.

### Permissions Detail

#### 👥 Manajemen Penyewa (Tenant)

| Aksi               | Permission | Catatan                                          |
| ------------------ | ---------- | ------------------------------------------------ |
| ✅ **View List**   | viewAny    | Melihat daftar penyewa                           |
| ✅ **View Detail** | view       | Melihat detail penyewa                           |
| ✅ **Create**      | create     | Mendaftarkan penyewa baru                        |
| ⚠️ **Edit**        | update     | Edit data biasa (tidak boleh edit data sensitif) |
| ❌ **Delete**      | delete     | ❌ Tidak boleh hapus penyewa                     |

**Data Sensitif (Tidak Boleh Edit):**

- ❌ Email
- ❌ Nomor Telepon
- ❌ Status Penyewa
- ❌ Data Identitas

**Data Boleh Edit:**

- ✅ Nama
- ✅ Alamat
- ✅ Informasi kontak non-sensitif
- ✅ Catatan

#### 💰 Manajemen Pembayaran (Payment)

| Aksi               | Permission | Status                          |
| ------------------ | ---------- | ------------------------------- |
| ✅ **View List**   | viewAny    | Bisa melihat daftar pembayaran  |
| ✅ **View Detail** | view       | Bisa melihat detail pembayaran  |
| ✅ **Create**      | create     | Membuat pembayaran baru         |
| ❌ **Edit**        | update     | ❌ Tidak boleh edit pembayaran  |
| ❌ **Confirm**     | confirm    | ❌ Hanya accountant/admin       |
| ❌ **Reject**      | reject     | ❌ Hanya admin                  |
| ❌ **Delete**      | delete     | ❌ Tidak boleh hapus pembayaran |

#### 📊 Manajemen Biaya Operasional (Operational Cost)

| Aksi               | Permission | Status                                 |
| ------------------ | ---------- | -------------------------------------- |
| ❌ **View List**   | viewAny    | ❌ Tidak boleh lihat biaya operasional |
| ❌ **View Detail** | view       | ❌ Tidak boleh lihat detail biaya      |
| ❌ **Create**      | create     | ❌ Hanya accountant                    |
| ❌ **Edit**        | update     | ❌ Tidak boleh edit                    |
| ❌ **Approve**     | approve    | ❌ Hanya admin                         |
| ❌ **Delete**      | delete     | ❌ Tidak boleh hapus                   |

#### 📈 Manajemen Laporan Keuangan (Financial Report)

| Aksi               | Permission | Status                              |
| ------------------ | ---------- | ----------------------------------- |
| ❌ **View List**   | viewAny    | ❌ Tidak boleh lihat laporan        |
| ❌ **View Detail** | view       | ❌ Tidak boleh lihat detail laporan |
| ❌ **Create**      | create     | ❌ Hanya accountant/admin           |
| ❌ **Edit**        | edit       | ❌ Tidak boleh edit                 |
| ❌ **Publish**     | publish    | ❌ Hanya admin                      |
| ❌ **Delete**      | delete     | ❌ Tidak boleh hapus                |

---

## 📊 Comparison Table: Role Permissions

### Tenant Management

| Aksi               | Admin | Accountant | Staff      |
| ------------------ | ----- | ---------- | ---------- |
| View List          | ✅    | ✅         | ✅         |
| View Detail        | ✅    | ✅         | ✅         |
| Create             | ✅    | ❌         | ✅         |
| Update (All)       | ✅    | ❌         | ⚠️ Partial |
| Update (Sensitive) | ✅    | ❌         | ❌         |
| Delete             | ✅    | ❌         | ❌         |
| Restore            | ✅    | ❌         | ❌         |
| Force Delete       | ✅    | ❌         | ❌         |

### Payment Management

| Aksi        | Admin | Accountant | Staff |
| ----------- | ----- | ---------- | ----- |
| View List   | ✅    | ✅         | ✅    |
| View Detail | ✅    | ✅         | ✅    |
| Create      | ✅    | ❌         | ✅    |
| Update      | ✅    | ⚠️ Limited | ❌    |
| Confirm     | ✅    | ✅         | ❌    |
| Reject      | ✅    | ❌         | ❌    |
| Delete      | ✅    | ❌         | ❌    |

### Operational Cost

| Aksi        | Admin | Accountant | Staff |
| ----------- | ----- | ---------- | ----- |
| View List   | ✅    | ✅         | ❌    |
| View Detail | ✅    | ✅         | ❌    |
| Create      | ✅    | ✅         | ❌    |
| Update      | ✅    | ⚠️ Limited | ❌    |
| Approve     | ✅    | ❌         | ❌    |
| Reject      | ✅    | ❌         | ❌    |
| Delete      | ✅    | ❌         | ❌    |

### Financial Report

| Aksi           | Admin | Accountant | Staff |
| -------------- | ----- | ---------- | ----- |
| View List      | ✅    | ✅         | ❌    |
| View Detail    | ✅    | ✅         | ❌    |
| Create         | ✅    | ✅         | ❌    |
| Update (Draft) | ✅    | ❌         | ❌    |
| Publish        | ✅    | ❌         | ❌    |
| Archive        | ✅    | ❌         | ❌    |
| Delete         | ✅    | ❌         | ❌    |

---

## 🔄 Role Hierarchy & Delegation

```
┌─────────────────────────────────────────┐
│            ADMIN (Tertinggi)            │
│  • Kontrol penuh semua modul            │
│  • Approval authority                   │
│  • System configuration                 │
│  • User management                      │
└──────────────────┬──────────────────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
┌───────▼────────┐  ┌────────▼─────────┐
│   ACCOUNTANT   │  │      STAFF       │
│ • Keuangan     │  │ • Operasional    │
│ • Laporan      │  │ • Customer Care  │
│ • Approval terbatas │ • Data entry   │
└────────────────┘  └──────────────────┘
```

---

## 🛡️ Authorization Checks (Authorization Policy)

Sistem menggunakan Laravel Authorization Policies untuk mengecek permission sebelum eksekusi aksi.

### Implementation Detail

#### Dalam Controller

```php
// Cek authorization sebelum update tenant
$this->authorize('update', $tenant);

// Atau dengan method name
if ($user->cannot('update', $tenant)) {
    abort(403, 'Unauthorized');
}
```

#### Dalam Blade Template

```php
@can('update', $tenant)
    <a href="...">Edit Tenant</a>
@endcan
```

#### Untuk Role Check Langsung

```php
// Check single role
if ($user->hasRole('admin')) {
    // Admin only
}

// Check multiple roles
if ($user->hasRole(['admin', 'staff'])) {
    // Admin or Staff
}

// Check specific methods
if ($user->isAdmin()) { }
if ($user->isAccountant()) { }
if ($user->isStaff()) { }
```

---

## 📝 Use Cases by Role

### 🔴 Admin Use Cases

1. **Onboarding User Baru**
    - Buat akun user
    - Set role (Admin/Accountant/Staff)
    - Configure permission

2. **Manage Kompleks Kos**
    - Lihat laporan lengkap
    - Approve pembayaran dan biaya
    - Manage semua tenant
    - Publikasi laporan keuangan

3. **System Monitoring**
    - View activity log
    - Monitor system health
    - Backup data

### 🟡 Accountant Use Cases

1. **Proses Pembayaran**
    - Melihat pembayaran masuk
    - Confirm pembayaran pending
    - Record transaksi

2. **Catat Biaya Operasional**
    - Tambah pengeluaran
    - Edit pengeluaran sendiri
    - Submit untuk approval

3. **Buat Laporan Keuangan**
    - Generate laporan bulanan
    - Submit ke admin untuk publish
    - Analisa cash flow

### 🟢 Staff Use Cases

1. **Daftar Penyewa Baru**
    - Input data tenant
    - Upload dokumen
    - Set room assignment

2. **Catat Pembayaran**
    - Record pembayaran cash/transfer
    - Input bukti pembayaran
    - Follow-up overdue

3. **Customer Service**
    - Update informasi tenant
    - Handle pertanyaan tenant
    - Manage tenant documents

---

## 🔐 Security Considerations

### Best Practices

1. **Principle of Least Privilege**
    - Staff hanya dapat akses data yang diperlukan
    - Accountant tidak bisa menghapus data
    - Admin approval untuk aksi sensitif

2. **Audit Trail**
    - Semua aksi dicatat di activity log
    - User dapat dilacak per aksi
    - Perubahan data tersimpan

3. **Data Sensitivity**
    - Email dan telepon penyewa hanya admin yang bisa edit
    - Financial data hanya accountant dan admin
    - Sensitive fields dienkripsi

4. **Session Management**
    - Auto-logout setelah 30 menit idle
    - Single session per user
    - IP whitelist untuk admin (opsional)

---

## 📋 Checklist: Assigning Roles

Ketika menambah user baru, pertimbangkan:

### Untuk Admin

- [ ] Sudah di-train penggunaan sistem
- [ ] Bisa handle semua modul
- [ ] Trusted dengan data sensitive
- [ ] Clear approval authority

### Untuk Accountant

- [ ] Sudah memahami akuntansi
- [ ] Reliable dengan keuangan
- [ ] Tidak perlu akses tenant operasional
- [ ] Trained in laporan keuangan

### Untuk Staff

- [ ] Customer service skills
- [ ] Data entry accuracy
- [ ] Familiar dengan prosedur tenant
- [ ] Direct report ke manager

---

## ⚠️ Common Mistakes & Solutions

| Masalah                       | ❌ Wrong                     | ✅ Correct                       |
| ----------------------------- | ---------------------------- | -------------------------------- |
| Give all staff admin access   | ❌ Terlalu banyak permission | ✅ Specific role sesuai job desc |
| Staff bisa edit data sensitif | ❌ Security risk             | ✅ Admin approval required       |
| No audit trail                | ❌ Can't track changes       | ✅ Log semua activity            |
| Shared accounts               | ❌ No accountability         | ✅ One account per person        |
| Never revoke old access       | ❌ Accumulating permissions  | ✅ Regular access review         |

---

## 🔗 Related Documentation

- [API Documentation](API_DOCUMENTATION.md) - Endpoints detail
- [TESTING_GUIDE.md](TESTING_GUIDE.md) - Testing dengan berbagai role
- [QUICK_REFERENCE.md](QUICK_REFERENCE.md) - Quick commands
- [README.md](README.md) - Main documentation

---

## 📞 Support & Questions

Untuk pertanyaan terkait:

- **Role Permission:** Hubungi Administrator
- **Account Access:** Contact HR/Manager
- **Policy Questions:** Refer to this documentation

---

**Last Updated:** January 2026
**Version:** 1.0
**Status:** ✅ Production Ready
