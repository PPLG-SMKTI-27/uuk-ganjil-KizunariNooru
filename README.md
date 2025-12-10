# SISWA IZIN SYSTEM (SIS)
## Sistem Manajemen Izin Siswa - Aplikasi Web Profesional Production-Ready

---

## 📋 Daftar Isi
1. [Deskripsi Proyek](#deskripsi-proyek)
2. [Fitur Utama](#fitur-utama)
3. [Teknologi](#teknologi)
4. [Setup & Instalasi](#setup--instalasi)
5. [Struktur Folder](#struktur-folder)
6. [Deployment](#deployment)
7. [Keamanan](#keamanan)
8. [Development](#development)
9. [Troubleshooting](#troubleshooting)

---

## 🎯 Deskripsi Proyek

SISWA IZIN SYSTEM (SIS) adalah aplikasi web manajemen izin siswa sekolah dengan desain profesional modern. Dibangun dengan **PHP 8+ OOP**, **MySQL 5.7+**, **Tailwind CSS 4**, dan standar keamanan tinggi (CSRF, bcrypt, prepared statements, input validation).

### ✨ Status: Production-Ready

---

## ✨ Fitur Utama

### Untuk Siswa
- ✅ Login/Logout aman
- ✅ Dashboard dengan ringkasan izin
- ✅ Ajukan izin dengan form lengkap
- ✅ Lihat riwayat izin & status
- ✅ Edit izin (status pending)
- ✅ Kelola profil akun

### Untuk Guru/Wali Kelas
- ✅ Dashboard dengan statistik izin
- ✅ Daftar izin siswa kelas
- ✅ Setujui/Tolak izin dengan komentar
- ✅ Filter izin berdasarkan status
- ✅ Laporan izin bulanan

### Untuk Admin
- ✅ Dashboard monitoring keseluruhan
- ✅ Kelola data siswa (CRUD)
- ✅ Kelola data guru/wali kelas
- ✅ Import data dari Excel
- ✅ Manajemen kelas
- ✅ Laporan global sistem

---

## 📁 Struktur Folder

```
uuk-ganjil-KizunariNooru/
├── App/
│   ├── Config/
│   │   ├── bootstrap.php          # Inisialisasi aplikasi
│   │   ├── config.php             # Konfigurasi konstanta
│   │   └── Database.php           # Koneksi database (Singleton)
│   ├── Controllers/
│   │   ├── AuthController.php     # Autentikasi & login
│   │   ├── SiswaController.php    # Logika siswa
│   │   ├── WaliController.php     # Logika guru/wali
│   │   ├── AdminController.php    # Logika admin
│   │   └── IzinController.php     # Logika izin (shared)
│   ├── Models/
│   │   ├── User.php               # Model user
│   │   ├── Siswa.php              # Model siswa
│   │   └── Izin.php               # Model izin
│   ├── Middleware/
│   │   └── Auth.php               # Guard & CSRF protection
│   ├── Helpers/
│   │   └── Validator.php          # Validasi & sanitasi
│   └── Views/
│       ├── Auth/
│       │   └── login.php
│       ├── siswa/
│       │   ├── dashboard.php
│       │   ├── create.php
│       │   ├── history.php
│       │   ├── profile.php
│       │   └── edit.php
│       ├── wali/
│       │   ├── index.php
│       │   └── detail.php
│       └── admin/
│           ├── index.php
│           ├── create_user.php
│           ├── edit_user.php
│           └── manage_data.php
├── Public/
│   ├── index.php                  # Main router
│   └── css/
│       └── main.css               # Responsive CSS
├── prototype/                     # Figma prototype HTML
├── perijinan_siswa.sql            # Database schema
└── README.md                      # Dokumentasi ini
```

---

## 🛠 Teknologi

| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| **PHP** | 8.0+ | Backend language |
| **MySQL** | 5.7+ | Database |
| **HTML5** | - | Markup |
| **CSS3** | - | Styling responsive |
| **JavaScript** | ES6+ | Frontend interactivity |

### Library/Framework
- **Prepared Statements** - Prevent SQL injection
- **Password Hash (bcrypt)** - Secure password storage
- **Session Management** - User authentication
- **CSRF Tokens** - Form security

---

## 🚀 Setup & Instalasi

### Prasyarat
- PHP 8.0 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Node.js 14+ (untuk Tailwind CSS)

### Development

```bash
# 1. Install dependencies
npm install

# 2. Build CSS
npm run build

# 3. Start development (watch CSS)
npm run dev

# 4. Access
# http://localhost/uuk-ganjil-KizunariNooru/
```

### Production

Lihat **[DEPLOYMENT.md](./DEPLOYMENT.md)** untuk:
- VPS deployment (Nginx)
- Shared hosting (cPanel)
- Docker setup
- Database migration
- Security hardening
- Web Server (Apache/Nginx)
- Composer (opsional)

### Langkah-langkah

#### 1. Clone/Download Project
```bash
git clone https://github.com/PPLG-SMKTI-27/uuk-ganjil-KizunariNooru.git
cd uuk-ganjil-KizunariNooru
```

#### 2. Setup Database
```bash
# Buka phpMyAdmin atau MySQL CLI
mysql -u root -p

# Create database
CREATE DATABASE perijinan_siswa;
USE perijinan_siswa;

# Import schema
SOURCE perijinan_siswa.sql;
```

#### 3. Konfigurasi
Edit `App/Config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'perijinan_siswa');
define('DB_USER', 'root');
define('DB_PASS', '');  // Ubah sesuai password MySQL Anda
```

#### 4. Jalankan Server
```bash
# Menggunakan PHP Built-in Server
php -S localhost:8000 -t Public/

# Atau menggunakan Apache dengan vhost pointing ke Public/
```

#### 5. Akses Aplikasi
```
http://localhost:8000/index.php?action=auth.login
```

---

## 🗄 Konfigurasi Database

### Akun Default

| Role | Email | Password | Status |
|------|-------|----------|--------|
| Admin | admin@example.com | password | Aktif |
| Wali Kelas | wali@example.com | password | Aktif |
| Siswa | siswa@example.com | password | Aktif |

**PENTING:** Ubah password default setelah instalasi pertama!

### Schema Tabel

#### `user`
```sql
CREATE TABLE user (
  id_user INT PRIMARY KEY AUTO_INCREMENT,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,  -- Hashed dengan bcrypt
  role ENUM('Admin','WaliKelas','Siswa')
);
```

#### `siswa`
```sql
CREATE TABLE siswa (
  id_siswa INT PRIMARY KEY AUTO_INCREMENT,
  id_user INT NOT NULL,
  nama_siswa VARCHAR(255),
  nisn VARCHAR(10) UNIQUE,
  nik VARCHAR(16),
  kelas VARCHAR(50),
  alamat VARCHAR(255),
  id_walikelas INT,
  FOREIGN KEY (id_user) REFERENCES user(id_user),
  FOREIGN KEY (id_walikelas) REFERENCES pegawai(id_pegawai)
);
```

#### `pegawai` (Guru/Wali Kelas)
```sql
CREATE TABLE pegawai (
  id_pegawai INT PRIMARY KEY AUTO_INCREMENT,
  id_user INT NOT NULL,
  nama VARCHAR(255),
  jabatan ENUM('Admin','WaliKelas'),
  FOREIGN KEY (id_user) REFERENCES user(id_user)
);
```

#### `tb_izin`
```sql
CREATE TABLE tb_izin (
  id_izin INT PRIMARY KEY AUTO_INCREMENT,
  id_siswa INT NOT NULL,
  keperluan VARCHAR(255),
  rencana_keluar DATETIME,
  rencana_kembali DATETIME,
  id_approve INT,
  status ENUM('pending','diizinkan','ditolak') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_siswa) REFERENCES siswa(id_siswa),
  FOREIGN KEY (id_approve) REFERENCES pegawai(id_pegawai)
);
```

---

## 👥 User Roles & Permissions

### SISWA
- **Dapat:**
  - Masuk ke dashboard pribadi
  - Ajukan izin baru
  - Edit izin (status pending)
  - Hapus izin (status pending)
  - Lihat riwayat & status izin
  - Edit profil akun
- **Tidak dapat:** Mengakses data siswa lain, approve izin, mengelola users

### WALI KELAS
- **Dapat:**
  - Masuk ke dashboard wali
  - Lihat daftar izin siswa kelasnya
  - Approve/Reject izin dengan komentar
  - Filter izin berdasarkan status
  - Ekspor laporan izin
- **Tidak dapat:** Menghapus izin, mengelola users, kelola data siswa

### ADMIN
- **Dapat:**
  - Akses dashboard monitoring global
  - CRUD semua user (siswa, guru, admin)
  - CRUD siswa & kelasnya
  - CRUD guru/wali kelas
  - Import data dari Excel
  - Lihat semua izin & statistik
  - Kelola pengaturan sistem
- **Tidak dapat:** Dibatasi akses penuh ke database

---

## 🔀 API Routes

### Authentication
```
GET  /index.php?action=auth.login         - Tampilkan form login
POST /index.php?action=auth.login         - Process login
GET  /index.php?action=auth.logout        - Logout
```

### Siswa
```
GET  /index.php?action=siswa.dashboard    - Dashboard siswa
GET  /index.php?action=siswa.create       - Form ajukan izin
POST /index.php?action=siswa.create       - Submit izin baru
GET  /index.php?action=siswa.history      - Riwayat izin
GET  /index.php?action=siswa.profile      - Profil siswa
POST /index.php?action=siswa.profile      - Update profil
GET  /index.php?action=siswa.deleteIzin   - Delete izin (pending)
```

### Wali Kelas
```
GET  /index.php?action=wali.index         - Dashboard wali
GET  /index.php?action=wali.detail?id=X   - Detail izin
POST /index.php?action=wali.approve       - Approve izin
POST /index.php?action=wali.reject        - Reject izin
```

### Admin
```
GET  /index.php?action=admin.index        - Dashboard admin
GET  /index.php?action=admin.createUser   - Form tambah user
POST /index.php?action=admin.storeUser    - Submit user baru
GET  /index.php?action=admin.editUser     - Form edit user
POST /index.php?action=admin.updateUser   - Submit update user
GET  /index.php?action=admin.deleteUser   - Delete user
```

---

## 💡 Panduan Penggunaan

### Untuk Siswa

1. **Login**
   - Masukkan email & password
   - Klik "Masuk"

2. **Dashboard**
   - Lihat ringkasan izin (menunggu, disetujui, ditolak)
   - Lihat riwayat izin terbaru
   - Akses menu cepat

3. **Ajukan Izin**
   - Klik "Ajukan Izin Baru"
   - Isi formulir (alasan, tanggal mulai, tanggal akhir)
   - Klik "Kirim Permohonan"
   - Tunggu persetujuan wali kelas (max 24 jam)

4. **Edit Izin**
   - Hanya bisa edit jika status PENDING
   - Klik tombol edit di riwayat
   - Ubah data & simpan

### Untuk Guru/Wali Kelas

1. **Login** dengan akun wali kelas

2. **Dashboard**
   - Lihat statistik izin siswa kelas
   - Lihat daftar izin menunggu persetujuan

3. **Persetujuan Izin**
   - Klik tombol "Detail" atau nama siswa
   - Review alasan & tanggal izin
   - Pilih "Setujui" atau "Tolak"
   - Tambah komentar (opsional)
   - Submit persetujuan

4. **Filter & Laporan**
   - Filter berdasarkan status izin
   - Ekspor laporan ke PDF/Excel

### Untuk Admin

1. **Login** dengan akun admin

2. **Dashboard**
   - Monitor total siswa, guru, izin hari ini
   - Lihat statistik sistem

3. **Kelola Data**
   - **Siswa:** CRUD siswa, assign ke wali kelas, import Excel
   - **Guru:** CRUD guru/wali kelas
   - **Kelas:** CRUD kelas sekolah

4. **Manajemen Izin**
   - Lihat semua izin dari seluruh kelas
   - Override persetujuan izin jika diperlukan

---

## 🔒 Keamanan

### Implementasi Keamanan

#### 1. **Input Validation & Sanitization**
- Server-side validation menggunakan `Validator` class
- Client-side validation menggunakan HTML5
- Sanitasi output dengan `htmlspecialchars()`
- Email validation dengan `filter_var()`

#### 2. **SQL Injection Prevention**
- **Prepared Statements** untuk semua query
- Parameter binding dengan type checking
- Database class menggunakan mysqli prepared statements

Contoh aman:
```php
$sql = "SELECT * FROM user WHERE email = ? AND role = ?";
$result = $db->fetchOne($sql, [$email, $role], 'ss');
```

#### 3. **Password Security**
- Hashing dengan **bcrypt** (PASSWORD_BCRYPT)
- Cost factor: 10
- Verifikasi dengan `password_verify()`

```php
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
password_verify($plainPassword, $hashedPassword);
```

#### 4. **CSRF Protection**
- Token generation di session
- Verificati di form submission
- `Csrf::verify()` helper untuk check token

```php
<?= Csrf::field() ?>  <!-- Hidden input di form -->
```

#### 5. **Session Security**
- Session ID regeneration setelah login
- Session timeout management
- Secure session cookie settings

#### 6. **XSS Prevention**
- `htmlspecialchars()` di semua output
- Avoid inline scripts
- Content Security Policy header

#### 7. **Authentication & Authorization**
- Role-based access control (RBAC)
- Guard middleware untuk protect routes
- User ownership verification

```php
Guard::requireRole('Siswa');  // Hanya siswa bisa akses
Guard::requireAnyRole(['Admin', 'WaliKelas']);  // Multiple roles
```

#### 8. **Error Handling**
- Suppress error display di production
- Log errors ke file
- Custom error pages

### Security Checklist
- [ ] Update password default di database
- [ ] Set file permissions 644 untuk .php, 755 untuk folder
- [ ] Disable directory listing di .htaccess
- [ ] Use HTTPS di production
- [ ] Regular backup database
- [ ] Monitor access logs
- [ ] Update PHP & MySQL versions

---

## 🐛 Troubleshooting

### Error: "Database connection failed"
**Solusi:**
- Cek konfigurasi di `App/Config/config.php`
- Pastikan MySQL service running
- Verifikasi username & password MySQL
- Pastikan database `perijinan_siswa` sudah dibuat

### Error: "Class not found"
**Solusi:**
- Pastikan path require statement benar (gunakan absolute path dengan `__DIR__`)
- Check folder struktur sesuai dokumentasi
- Clear session cache

### Error: "Invalid CSRF token"
**Solusi:**
- Pastikan form include `<?= Csrf::field() ?>`
- Check session active dengan `session_start()`
- Clear browser cookies & try again

### Izin tidak muncul di dashboard
**Solusi:**
- Pastikan data siswa sudah create di database
- Cek relationship `user -> siswa`
- Query database: `SELECT * FROM siswa WHERE id_user = X`

### Login gagal
**Solusi:**
- Pastikan password benar (case-sensitive)
- Check database field `user.password` ter-hash bcrypt
- Cek role user di `user.role`

### Mobile tidak responsive
**Solusi:**
- Clear browser cache
- Ensure viewport meta tag exist di head
- Check CSS media queries di `Public/css/main.css`

---

## 📞 Support & Kontribusi

### Reporting Issues
Jika menemukan bug:
1. Deskripsi masalah dengan detail
2. Screenshot/error message
3. Steps untuk reproduce
4. Environment info (PHP version, MySQL, OS)

### Kontribusi
Untuk kontribusi:
1. Fork repository
2. Buat branch feature (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 Lisensi

Proyek ini menggunakan lisensi MIT. Silakan gunakan secara bebas untuk tujuan edukatif dan komersial.

---

## 👨‍💻 Author

**Dikembangkan oleh:** PPLG-SMKTI-27  
**Last Updated:** December 2025  
**Version:** 1.0.0

---

## 📌 Catatan Penting

1. **Password Default** - Ubah segera setelah instalasi
2. **HTTPS** - Gunakan di production environment
3. **Backup** - Regular backup database Anda
4. **Updates** - Check repository untuk updates & security patches
5. **Support** - Hubungi admin untuk bantuan teknis

---

**Selamat menggunakan SISWA IZIN SYSTEM! 🎉**
