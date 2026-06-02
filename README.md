# 💰 SobatSaldo

SobatSaldo adalah aplikasi pencatatan keuangan (Financial Tracking App) berbasis web modern yang dirancang untuk memudahkan pengguna melacak pemasukan, pengeluaran, mutasi dompet, hingga utang/piutang secara cerdas, cepat, dan terorganisir. Dibangun dengan fokus pada kecepatan (UX) dan keamanan data yang ketat.

---

## ✨ Fitur Utama

### 1. 🚀 Pencatatan Super Cepat (Ghost Input & NLP)
- **Ghost Input / Natural Language:** Catat transaksi semudah mengetik kalimat sehari-hari (contoh: "makan siang 25k"). Sistem akan mengekstrak nominal, deskripsi, dan mengkategorikannya secara otomatis.
- **Quick Habits:** Tombol pintas cerdas untuk transaksi yang sering berulang (seperti beli bensin, token listrik) lengkap dengan *icon* emoji dinamis yang otomatis menyesuaikan kata kunci.
- **Tabbed UI Khusus:** Pisahkan secara rapi mode pencatatan Reguler, Mutasi (Top-up/Transfer antar dompet), dan Hutang/Piutang untuk mencegah kebingungan pengguna.

### 2. 📱 Progressive Web App (PWA) & Mode Offline
- Bisa di-install layaknya aplikasi native di Android/iOS/Desktop.
- **Dukungan Offline Penuh:** Anda tetap bisa mencatat pengeluaran di pedalaman tanpa sinyal. Data akan disimpan secara lokal (*IndexedDB*) dan otomatis disinkronisasikan ke server (Background Sync) begitu internet kembali menyala.
- Notifikasi *Haptic feedback* (getar) setiap transaksi berhasil dicatat di perangkat yang mendukung.

### 3. 🛡️ Keamanan Kelas Level-Bank
- **Proteksi Brute-Force & Bot:** Sistem secara agresif memblokir Alamat IP (IP Address) selama 15 menit jika terdeteksi 5 kali gagal *login*, dengan pengalihan paksa ke Halaman *Error 429 - Too Many Requests* dinamis.
- **Manajemen Sesi Tunggal:** Setiap pengguna hanya diizinkan *login* di satu perangkat. Jika terdeteksi *login* di perangkat baru, sesi di perangkat lama akan di-*kick* (Logout) secara otomatis.
- **Validasi Password Super Ketat:** Mendeteksi penggunaan password yang lemah atau password yang pernah bocor di internet (Pwned Data Leaks).

### 4. 📊 Dashboard & Analitik Menyeluruh
- Pantau Sisa Saldo Bulan Lalu (Rollover Budget), Batas Aman Pengeluaran Harian, serta pencapaian "Streak" mencatat.
- Visualisasi top kategori pengeluaran bulan ini yang mudah dipahami.
- Pengelolaan dompet multi-sumber (Tunai, Bank, e-Wallet).

### 5. 🇮🇩 100% Dilokalisasi (Bahasa Indonesia)
- Pesan kesalahan (error validation), tampilan halaman, hingga *Template Email Notifikasi Sistem* (Register, Reset Password, Verify Email) sepenuhnya terjemahan bahasa Indonesia, berpadu dengan desain estetik SobatSaldo.

---

## 🛠️ Stack Teknologi

- **Backend:** Laravel 11 (PHP 8.x)
- **Frontend:** Laravel Blade, Alpine.js (Reactive UI), Tailwind CSS v3
- **Database:** MySQL / SQLite
- **Lainnya:** Vite (Asset Bundler)

---

## ⚙️ Langkah-Langkah Instalasi

Untuk menjalankan *SobatSaldo* di server lokal Anda (Windows/Mac/Linux), ikuti instruksi berikut:

### 1. Kloning Repositori & Masuk Direktori
```bash
git clone https://github.com/username/sobatsaldo.git
cd sobatsaldo
```

### 2. Instalasi Dependensi (Backend & Frontend)
Pastikan Anda sudah menginstal **Composer** dan **Node.js/NPM**.
```bash
composer install
npm install
```

### 3. Konfigurasi File *Environment* (.env)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan atur parameter berikut:
```env
APP_NAME=SobatSaldo
APP_LOCALE=id

# Konfigurasi Database Anda
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sobatsaldo
DB_USERNAME=root
DB_PASSWORD=

# Pastikan Email telah disetel dengan mail provider Anda untuk fitur reset/verify (misal Mailtrap/SMTP)
```

### 4. *Generate* Key & Jalankan Migrasi Basis Data
```bash
php artisan key:generate
php artisan migrate
```

### 5. Proses *Compile* Asset UI
Jalankan Vite untuk mem-build Tailwind CSS & Javascript.
```bash
npm run build
# Atau gunakan `npm run dev` jika sedang tahap pengembangan
```

### 6. Jalankan Server Lokal Laravel
```bash
php artisan serve
```
Aplikasi kini dapat diakses di browser melalui: `http://localhost:8000`

---

## 👨‍💻 Pengembang

**Developed by : Muhammad Ilfan Asykuri**

Dibuat dengan ❤️ untuk membantu Anda mengelola finansial dengan lebih bijak.
