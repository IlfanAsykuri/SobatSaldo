**PRODUCT REQUIREMENTS DOCUMENT (PRD)**

**Project Name:** Frictionless Financial Tracker (PWA)

**Platform:** Web App (Mobile-First PWA & Desktop Dashboard)

**Tech Stack:** Laravel 13, MySQL, JavaScript (Service Worker + IndexedDB), TailwindCSS

**Date:** 1 Juni 2026

**1. Executive Summary**

**1.1 Visi Produk**

Menciptakan asisten pencatat keuangan pribadi yang mengeliminasi 100% hambatan (*friction*) dalam proses *input* data harian. Aplikasi ini dirancang untuk beradaptasi dengan kebiasaan bahasa pengguna, bukan memaksa pengguna beradaptasi dengan form UI yang kaku, serta menjaga kesehatan psikologis pengguna dengan pendekatan bebas rasa bersalah (*guilt-free*).

**1.2 Masalah yang Diselesaikan**

- **Faktor Malas:** Mengisi *form* panjang (kategori, tanggal, nominal, tipe) untuk transaksi kecil (misal: parkir, es teh).
- **Akurasi vs Hambatan:** Kebutuhan memecah transaksi detail (misal: barang + biaya admin) yang merepotkan.
- **Kendala Sinyal:** Gagal mencatat pengeluaran di area *blank spot* (misal: *basement*, minimarket).
- **Kelelahan Mental:** *Dashboard* keuangan konvensional memicu rasa bersalah dan *information overload*.

**2. Core Principles (Asas Desain)**

- **Zero Clicks Input:** Aplikasi dibuka, *keyboard* langsung aktif.
- **Forgiving UI (Antarmuka Pemaaf):** Toleransi *typo* tinggi, tidak ada pesan *error* wajib, *fallback* ke kategori "Lain-lain".
- **Offline-First:** Harus bisa digunakan tanpa koneksi internet sama sekali.
- **Adaptive Environment:** HP untuk eksekusi cepat (*input*), Laptop untuk evaluasi komprehensif (*analytics*).

**3. Functional Requirements (Kebutuhan Fungsional)**

**3.1 Sistem Input Berbasis Teks (Natural Language)**

- **Single Input Field:** Satu kolom input teks untuk semua jenis transaksi.
- **Regex Parsing Engine:**
  - Ekstraksi Nominal: Mendeteksi angka di akhir *string*, mendukung format k (contoh: 20k = 20000) dan pemisah ribuan (contoh: 20.000 = 20000).
  - Ekstraksi Biaya Admin (Smart Syntax): Mendeteksi pola + [angka]. Contoh: beli sepatu 150k + 2k akan disimpan sebagai dua *record* (Barang 150.000 dan Admin 2.000).
- **Deteksi Pemasukan vs Pengeluaran:**
  - *Default*: Semua input adalah pengeluaran.
  - *Trigger*: Teks yang diawali simbol + atau mengandung kata kunci *income* (contoh: cair, gaji) otomatis diset sebagai Pemasukan.

**3.2 Smart Dictionary & Auto-Learn (Kategorisasi Otomatis)**

- **Pencocokan Kata Kunci:** Sistem mengekstrak kata dari deskripsi (misal: bayar publikasi jurnal KLIK) dan mencocokkan dengan tabel keyword\_dictionaries.
- **Fallback Category:** Jika tidak ada kata yang cocok, transaksi otomatis masuk ke kategori "Lain-lain" tanpa memunculkan *error*.
- **Auto-Learn Mechanism:** Saat pengguna memindahkan transaksi dari "Lain-lain" ke kategori spesifik (misal: memindahkan "jurnal KLIK" ke kategori "Pendidikan"), sistem otomatis menyaring *stopword* dan mendaftarkan kata unik (jurnal, KLIK) ke *dictionary* untuk pengenalan masa depan.

**3.3 Quick Habits Buttons (Makro Pintas)**

- Tombol pintas di bawah kolom input untuk pengeluaran repetitif harian.
- *Behavior*: **Tap-to-Populate**. Saat tombol diklik, teks otomatis terisi ke dalam kolom input (misal: parkir |), menyisakan kursor agar pengguna bisa mengetikkan nominal dinamis sebelum menekan *Enter*.

**3.4 Arsitektur Offline (PWA)**

- **Service Worker Interception:** Menangkap *request insert* saat status jaringan *offline*.
- **IndexedDB Queuing:** Menyimpan data transaksi mentah ke dalam penyimpanan lokal *browser* saat *offline*.
- **Background Sync:** Otomatis mengirimkan antrean data dari IndexedDB ke *server* Laravel segera setelah koneksi internet pulih, tanpa intervensi pengguna.

**3.5 Psikologi Notifikasi & UX**

- **The Streak (Gamifikasi):** Menampilkan rentetan hari berturut-turut (*streak* 🔥) pengguna melakukan *input*.
- **Safe-to-Spend Metric:** Menampilkan batas aman pengeluaran harian, bukan sekadar total sisa saldo pasif.
- **Web Push Notifications:** Pengingat harian (misal pukul 20:00) dengan *copywriting* asisten pribadi, memprioritaskan konsistensi mencatat.

**4. Antarmuka Adaptif (UI/UX)**

**4.1 Mobile View (Smartphone Viewport)**

- **Fokus:** Kecepatan *Input* (Eksekusi).
- **Layout:**
  - Atas: Indikator *Streak* & *Safe-to-Spend* harian (*muted text*).
  - Tengah: *Input field* raksasa (*autofocus*).
  - Bawah: Daftar 3-5 tombol *Quick Habits*, diikuti daftar riwayat 3 transaksi terakhir dengan efek animasi kedip hijau saat berhasil.
- **Hidden:** Tabel data lengkap, grafik *pie/line*, manajemen kategori (disembunyikan via CSS).

**4.2 Desktop View (Laptop Viewport)**

- **Fokus:** Analisis & Manajemen.
- **Layout:**
  - *Sidebar/Header:* Navigasi lengkap.
  - *Dashboard Storytelling:* Kalimat sapaan dinamis (misal: "Bulan ini kamu lebih hemat Rp 500k.").
  - *Main Content:* Tabel riwayat transaksi lengkap dengan filter *Date Range*, grafik interaktif (Top 3 Kategori), dan panel notifikasi untuk merapikan kategori "Lain-lain" (memicu *Auto-Learn*).
  - *Dictionary Manager:* Modul CRUD untuk melihat dan menghapus *keyword* yang dipelajari sistem (misal: menghapus *keyword* salah dari memori seperti server zabitsa jika ingin diubah ke kategori lain).

**5. Arsitektur Database (High-Level Schema)**

Tabel berikut merangkum struktur relasional utama untuk mendukung logika *parsing* cerdas.

|**Table Name**|**Column**|**Type**|**Description**|
| :- | :- | :- | :- |
|categories|id|PK|Primary Key|
||name|String|Nama kategori (misal: Makanan, Pendidikan, Belanja)|
||type|Enum|'income' atau 'expense'|
||is\_default|Boolean|Menandai kategori darurat/fallback ("Lain-lain")|
|keyword\_dictionaries|id|PK|Primary Key|
||category\_id|FK|Relasi ke categories.id|
||keyword|String|Kata tunggal/frasa (misal: jurnal, cair, parkir)|
|transactions|id|PK|Primary Key|
||category\_id|FK|Relasi ke categories.id|
||raw\_text|String|Teks asli yang diketik user (misal: bayar jurnal KLIK 350k)|
||amount|Decimal|Nominal angka bersih (misal: 350000)|
||type|Enum|'income' atau 'expense'|
||created\_at|Timestamp|Waktu transaksi terjadi|

**6. Rencana Eksekusi Pengembangan (Milestones)**

- **Fase 1: Engine Foundation (Backend)**
  - Setup *migrations*, *models*, dan relasi *database*.
  - Pembuatan modul Regex/NLP di *Controller* untuk memecah *string* teks dan angka, termasuk dukungan fitur biaya admin (+).
- **Fase 2: Smart Logic (Middle Tier)**
  - Implementasi logika *Fuzzy Matching* atau pencarian kata kunci ke keyword\_dictionaries.
  - Pembuatan fungsi *Auto-Learn* saat kategori di-*update*.
- **Fase 3: Adaptive Frontend & UI**
  - Desain responsif TailwindCSS (Mobile vs Desktop).
  - Implementasi AJAX/Fetch API agar *input* di HP bersifat *Zero-Refresh*.
- **Fase 4: PWA & Offline Capability**
  - Generasi manifest.json.
  - Konfigurasi *Service Worker* dan logika IndexedDB untuk antrean data *offline*.

