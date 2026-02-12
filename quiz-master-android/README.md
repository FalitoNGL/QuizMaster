# 📱 QuizMaster Mobile - Android Client

Aplikasi native Android untuk platform QuizMaster, dibangun dengan **Kotlin** dan **Jetpack Compose**. Aplikasi ini berfungsi sebagai interface mobile yang terintegrasi penuh dengan QuizMaster Backend API.

![Kotlin](https://img.shields.io/badge/Kotlin-1.9+-7F52FF?logo=kotlin&logoColor=white)
![Compose](https://img.shields.io/badge/Jetpack--Compose-1.5+-4285F4?logo=jetpackcompose&logoColor=white)
![Material3](https://img.shields.io/badge/Material--3-UI-6750A4?logo=materialdesign&logoColor=white)
![Retrofit](https://img.shields.io/badge/Retrofit-REST--API-00CED1)

---

## 📋 Fitur Utama

| ID | Fitur | Deskripsi |
|----|---------|-----------|
| **FR-M01** | **Category Selection** | Memilih kategori kuis berdasarkan data dari backend API. |
| **FR-M02** | **Dynamic Quiz** | Mengambil dan menampilkan soal secara dinamis sesuai kategori. |
| **FR-M03** | **Precision Header** | Redesigned Top Stats Bar dengan circular timer dan label kontras tinggi. |
| **FR-M04** | **Interactive Scoring** | Sinkronisasi logika skor (`100 + timeLeft`) dengan backend Laravel. |
| **FR-M05** | **Streak & Bonus** | Perhitungan streak jawaban benar untuk optimalisasi skor. |
| **FR-M06** | **Leaderboard** | Sinkronisasi peringkat global dengan data web platform. |
| **FR-M07** | **User Statistics** | Visualisasi performa user meliputi IQ, akurasi, dan level. |

---

## 🚀 Panduan Instalasi

### Prerequisites
- **Android Studio** (Koala atau versi terbaru)
- **JDK** 17+
- **Android SDK** API 24+

### Langkah-langkah Setup
1.  **Clone & Open**: Masukkan folder `quiz-master-android` ke dalam Android Studio.
2.  **Gradle Sync**: Jalankan proses *sync* Gradle dan tunggu hingga selesai.
3.  **API Configuration**: Buka file `RetrofitClient.kt` di sub-folder `data/api/` dan sesuaikan `BASE_URL`:
    ```kotlin
    private const val BASE_URL = "http://<IP_ANDA>:8000/api/"
    ```
4.  **Build**: Jalankan `Build > Make Project` atau langsung klik `Run`.

---

## 📡 Integrasi REST API

| Method | Endpoint | Kegunaan |
|--------|----------|----------|
| GET | `/api/categories` | Sinkronisasi daftar kategori aktif |
| GET | `/api/quiz/{id}` | Fetch bank soal berdasarkan ID kategori |
| POST | `/api/quiz/submit` | Submission jawaban untuk scoring di server |
| GET | `/api/stats` | Pengambilan data statistik performa user |

---

## 👨‍💻 Author
**FalitoNGL**
