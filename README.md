# 🗺️ SIG SPBU - Sistem Informasi Geografis Pemetaan SPBU Kab. Asahan

Sebuah aplikasi berbasis web (WebGIS) yang dirancang untuk memetakan dan menyajikan informasi letak geografis Stasiun Pengisian Bahan Bakar Umum (SPBU) di wilayah Kabupaten Asahan. Aplikasi ini dilengkapi dengan fitur analisis spasial cerdas untuk memudahkan pengguna menemukan SPBU terdekat.

## 🛠️ Teknologi & Tools

![HTML5](https://img.shields.io/badge/html5-%23E34F26.svg?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/css3-%231572B6.svg?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/javascript-%23323330.svg?style=for-the-badge&logo=javascript&logoColor=%23F7DF1E)
![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/mysql-%2300000F.svg?style=for-the-badge&logo=mysql&logoColor=white)
![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)
![Leaflet](https://img.shields.io/badge/Leaflet-199900?style=for-the-badge&logo=Leaflet&logoColor=white)

Proyek ini dibangun menggunakan arsitektur *Client-Server* standar:
* **Frontend:** HTML5, CSS3, JavaScript (Vanilla/jQuery)
* **Library Peta:** Leaflet.js (OpenStreetMap)
* **Backend:** PHP (Native / Framework)
* **Database:** MySQL
* **Environment / Web Server:** XAMPP (Apache)

## ✨ Fitur Utama

* **📍 Peta Interaktif:** Visualisasi penyebaran lokasi SPBU dengan titik koordinat presisi menggunakan teknologi **Leaflet.js** yang ringan dan cepat.
* **🔍 Pencarian SPBU:** Fitur pencarian pintar untuk mencari nama atau alamat SPBU dengan cepat dari daftar yang tersedia.
* **⭕ Analisis Radius Buffer:** Kemampuan analisis spasial cerdas untuk melihat jangkauan area SPBU terdekat dalam radius **3 KM** dan **5 KM** dari lokasi titik acuan.
* **🔖 Simpan Lokasi Favorit:** Pengguna terdaftar (User) dapat dengan mudah menyimpan lokasi SPBU favorit untuk mempercepat pencarian rute di kemudian hari.
* **🔐 Autentikasi Pengguna:** Sistem Login dan Registrasi pengguna baru dengan antarmuka yang modern dan aman.

## 📸 Pratinjau Aplikasi

### 1. Halaman Utama & Peta Interaktif
Halaman landing page yang menampilkan seluruh titik SPBU di Kabupaten Asahan beserta daftar lokasi di sidebar kiri.

<img width="1024" height="575" alt="image" src="https://github.com/user-attachments/assets/527ad0da-87c6-43ca-967e-adbdfc6a116a" />


### 2. Dashboard Pengguna & Analisis Radius
Tampilan dashboard pengguna saat mengaktifkan fitur **Radius Buffer 5 KM** pada SPBU tertentu beserta detail koordinat (Latitude/Longitude).
<img width="1024" height="581" alt="image" src="https://github.com/user-attachments/assets/a06c778c-996b-4621-9489-6bb88606982d" />
Tampilan dashboard pengguna saat mengaktifkan fitur **Radius Buffer 3 KM** pada SPBU tertentu beserta detail koordinat (Latitude/Longitude).
<img width="1024" height="545" alt="image" src="https://github.com/user-attachments/assets/8c68b298-8f16-49c0-aeac-46f42f23cff1" />


### 3. Fitur Utama & Keunggulan Sistem
Informasi mengenai fitur-fitur spasial canggih yang tersedia di dalam aplikasi.

<img width="1912" height="1007" alt="image" src="https://github.com/user-attachments/assets/48fef250-7b12-4cc4-afe4-0d8fdd62c2ea" />


### 4. Halaman Login & Registrasi
Antarmuka pendaftaran akun baru dan login yang responsif.

<img width="1024" height="534" alt="image" src="https://github.com/user-attachments/assets/69931397-3144-4c3e-92d4-7a507dfbbaa1" />


## 🚀 Cara Menjalankan Aplikasi Secara Lokal

Untuk menjalankan proyek ini di komputer Anda, ikuti langkah-langkah berikut:

1. **Persiapan Server:** Pastikan Anda telah menginstal **XAMPP** di komputer Anda.
2. **Kloning Repositori:**
   ```bash
   git clone https://github.com/username-anda/nama-repo-anda.git
   ```
   Atau unduh file ZIP dan ekstrak.
3. **Pindahkan Folder:** Pindahkan folder proyek ke dalam direktori `htdocs` pada instalasi XAMPP Anda (biasanya di `C:\xampp\htdocs\`). Ubah nama folder menjadi `sig-spbu` (opsional).
4. **Siapkan Database:**
   * Buka XAMPP Control Panel dan jalankan module **Apache** dan **MySQL**.
   * Buka browser dan akses `http://localhost/phpmyadmin/`.
   * Buat database baru (misalnya dengan nama `db_sig_spbu`).
   * Pilih opsi **Import** dan masukkan file database berformat `.sql` yang telah disediakan di dalam folder proyek ini.
5. **Konfigurasi Koneksi:**
   * Buka file konfigurasi database (biasanya bernama `koneksi.php` atau di folder `config`).
   * Sesuaikan *username*, *password*, dan nama database dengan pengaturan XAMPP Anda.
6. **Jalankan Aplikasi:**
   * Buka browser dan ketikkan alamat URL: `http://localhost/sig-spbu/`
   * Aplikasi siap digunakan!

## 🤝 Kontribusi

Jika Anda ingin berkontribusi pada pengembangan aplikasi ini, silakan lakukan *Fork* repositori ini, buat *branch* baru, dan kirimkan *Pull Request*.

## 📄 Lisensi

[MIT License](LICENSE)
