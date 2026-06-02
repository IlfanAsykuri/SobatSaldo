**UI/UX PRODUCT REQUIREMENTS DOCUMENT (PRD)**

**Project:** Frictionless Financial Tracker (PWA) **Design Philosophy:** Premium, *Clean*, *Fluid* (Setara "wondr by BNI") **Tech Stack UI:** Tailwind CSS v4, Alpine.js, Laravel Livewire v3 (wire:navigate) **Theme Default:** Light Mode (dengan *toggle* Dark Mode)

**1. Design System & Guideline (Sistem Desain)**

**1.1 Tipografi & Bentuk**

- **Font Family:** *Plus Jakarta Sans* atau *Inter* (Geometris, mudah dibaca sekilas).
- **Hierarki Teks:**
  - *Heading/Nominal:* Sangat besar & tebal (text-4xl font-extrabold).
  - *Label/Subtitle:* Abu-abu sedang (text-gray-500 font-medium).
- **Shape & Border:**
  - Radius sudut besar: rounded-2xl atau rounded-3xl untuk *card* dan *button*.
  - *No Hard Borders:* Gunakan pembatas berbasis bayangan lembut (shadow-sm, shadow-md) atau *background* solid yang kontras ringan (misal: putih di atas abu-abu sangat muda bg-gray-50).

**1.2 Palet Warna (Adaptive Light/Dark)**

- **Light Mode (Default):**
  - *Background Utama:* bg-slate-50 (Abu-abu sangat muda, bukan putih murni agar mata tidak cepat lelah).
  - *Card/Surface:* bg-white (Putih murni).
  - *Primary Brand:* bg-emerald-600 (Hijau elegan untuk tombol utama dan status aman).
  - *Warning/Expense:* bg-rose-500 (Hanya digunakan untuk indikator kritis, bukan warna *default* teks pengeluaran).
- **Dark Mode:**
  - *Background Utama:* bg-slate-900
  - *Card/Surface:* bg-slate-800
  - *Text Default:* text-slate-100

**1.3 Komponen Interaktif Utama**

- **Bottom Sheet:** Pengganti *modal pop-up* tradisional. Segala detail transaksi atau pengaturan muncul meluncur dari bawah layar (Mobile) atau panel sisi kanan (Desktop).
- **Ghost Input:** Kolom teks tanpa kotak pembatas tegas. Menyatu dengan *background* dan membesar secara visual saat kursor aktif (*focus state*).
- **Skeleton Loading:** Tidak ada *spinner* bulat klasik. Gunakan *skeleton* abu-abu berkedip halus saat menunggu data dimuat.

**2. Page Inventory (Daftar Halaman & Screen)**

Berikut adalah daftar absolut semua halaman yang harus dibuat, dari alur pendaftaran hingga halaman *error*.

**2.1 Alur Autentikasi (Auth & Onboarding)**

- **1. Halaman Splash / Landing Page PWA:** Layar pembuka sesaat (1 detik) dengan logo aplikasi di tengah saat PWA pertama kali diketuk dari layar HP.
- **2. Halaman Login:**
  - *Form:* Email, Password.
  - *Aksi:* Tombol "Masuk", Link "Lupa Password?", Link "Belum punya akun?".
  - *Logic:* "Remember Me" aktif otomatis di belakang layar (Sesi 30 Hari).
- **3. Halaman Register:**
  - *Form:* Nama Panggilan (singkat), Email, Password, Konfirmasi Password.
- **4. Halaman Lupa Password (Request):**
  - *Input:* Email pengguna.
  - *Aksi:* Tombol "Kirim Link/OTP Reset".
- **5. Halaman Konfirmasi Pengiriman (Check Your Email):**
  - *UI:* Ilustrasi amplop email, pesan "Kami telah mengirimkan instruksi ke email Anda", tombol "Kembali ke Login".
- **6. Halaman Reset Password (New Password):**
  - *Form:* Input password baru dan konfirmasi. (Diakses via link dari email).
- **7. Halaman Verifikasi Email (Opsional/Breeze Default):** Menahan pengguna mengakses *dashboard* utama sebelum mereka mengklik link di email mereka.

**2.2 Alur Utama (Mobile Viewport - PWA)**

- **8. Layar Utama (Input Workspace):**
  - *Header:* Indikator *Streak* (🔥 Hari ke-5) dan *Safe-to-Spend* Harian (Ukuran besar).
  - *Body:* Satu kolom input raksasa di tengah layar (*Autofocus*).
  - *Footer:* 4 Tombol *Quick Habits* melingkar (Tap-to-populate), dan 3 *list* riwayat transaksi terakhir.
- **9. Layar Evaluasi (Dashboard/Report):**
  - *Akses:* Geser layar ke kiri atau ketuk tab navigasi bawah.
  - *Header:* Kalimat *Storytelling* dinamis (Contoh: "Bulan ini kamu aman!").
  - *Chart:* *Donut Chart* minimalis menampilkan Top 3 Kategori Pengeluaran.
  - *Alert Panel:* Notifikasi khusus "Ada 2 transaksi belum dikategorikan" (Jika diketuk, membuka *Bottom Sheet*).
- **10. Layar Profil & Pengaturan:**
  - *Aksi:* Toggle *Dark/Light Mode*, Manajemen Kamus (melihat kata kunci yang dipelajari sistem), tombol *Logout*.
- **11. Bottom Sheet: Detail Transaksi:**
  - Muncul dari bawah saat satu transaksi di riwayat diketuk.
  - *Isi:* Teks asli (*raw input*), nominal, kategori, tanggal/jam, tombol "Hapus" dan tombol "Ubah Kategori".
- **12. Bottom Sheet: Rapikan Kategori (Auto-Learn Trigger):**
  - Muncul saat pengguna menyelesaikan transaksi di keranjang "Lain-lain".
  - *Isi:* Form pilihan kategori *dropdown* dan konfirmasi pemindahan.

**2.3 Alur Adaptif (Desktop Viewport - Web)**

- **13. Halaman Unified Dashboard:**
  - (Tampilan ini menggabungkan halaman 8, 9, dan 10 ke dalam satu layar lebar).
  - *Sidebar (Kiri):* Navigasi (Input, Laporan, Kamus, Pengaturan).
  - *Main Panel (Tengah):* Tabel riwayat transaksi lengkap dengan fitur Filter (Tanggal, Jenis) dan Paginasi.
  - *Widget Panel (Kanan):* Kolom Input Teks selalu *standby* di pojok kanan atas, di bawahnya terdapat panel *Safe-to-Spend* dan *Quick Habits*.
- **14. Halaman Dictionary Manager (Manajemen Kamus):**
  - Tabel *CRUD* bergaya admin *dashboard* premium untuk mengedit kata kunci (*keyword*) spesifik dan relasinya ke Kategori.

**2.4 Halaman Error & Utilitas (Penanganan Edge-Cases)**

- **15. Halaman 404 (Not Found):**
  - *UI:* Ilustrasi minimalis (misal: dompet kosong), teks "Halaman tidak ditemukan", dan satu tombol besar "Kembali ke Beranda" bergaya *rounded*.
- **16. Halaman 500 (Server Error):**
  - *UI:* Ilustrasi teknis halus, teks "Ups, sistem kami sedang merapikan catatan. Coba sesaat lagi."
- **17. Status Layar Offline (First Load Tanpa Cache):**
  - Hanya muncul jika pengguna *clear cache* lalu mencoba membuka web tanpa internet.
  - *UI:* Ikon Wi-Fi dicoret, teks "Kamu sedang offline. Hubungkan ke internet untuk memuat aplikasi pertama kali."
- **18. Blank State / Empty State (Kondisi Kosong):**
  - *UI:* Jika pengguna baru mendaftar dan belum ada transaksi. Teks: "Belum ada catatan hari ini. Mulai ketik pengeluaran pertamamu!" tanpa tabel kosong atau garis-garis yang kaku.

**3. Interaksi & Micro-Interactions (Faktor "Premium")**

Elemen-elemen kecil ini wajib diimplementasikan untuk memberikan ilusi bahwa aplikasi ini adalah aplikasi *native* kelas atas, bukan sekadar *website* biasa.

- **Haptic Feedback (Getaran):** Menggunakan JavaScript navigator.vibrate().
  - Sukses Input: Getaran tunggal singkat (50ms).
  - Error/Gagal: Getaran ganda (50ms, jeda 30ms, 50ms).
- **Livewire Navigation Transition:** Setiap perpindahan halaman utama (misal: dari *Workspace* ke *Dashboard*) tidak boleh ada kedipan putih. Gunakan transisi *fade-in/fade-out* singkat (200ms) menggunakan atribut navigasi SPA milik Livewire.
- **Toasts Notification (Notifikasi Mengambang):**
  - Muncul dari atas layar (Mobile) atau pojok kanan bawah (Desktop).
  - Warna: Hijau lembut (Sukses), Merah lembut (Gagal).
  - Durasi: Hilang otomatis dalam 3 detik tanpa perlu tombol "X" (*dismiss*).
- **Auto-Scroll Input:** Saat *keyboard* HP muncul, layar harus secara otomatis menggulir (*scroll*) agar kolom teks berada tepat di atas batas *keyboard*, tidak tertutup oleh tombol *Quick Habits*.
- **Offline Indicator Queue:** Saat status *offline*, warna tombol *Enter/Submit* berubah menjadi kuning redup atau ikon awan dengan tanda panah ke atas, memberikan sinyal visual bahwa data dikirim ke penyimpanan lokal (IndexedDB) dan mengantre.

