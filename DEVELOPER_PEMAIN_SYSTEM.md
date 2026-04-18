# 🎮 Koleksi Game - Developer & Pemain System

## 📋 Fitur Sistem

### 🎮 **Developer** 
- Bisa **membuat game baru** untuk database
- Game yang dibuat otomatis menjadi **tersedia** untuk semua pemain
- Bisa lihat semua game yang mereka buat di dashboard

### 👤 **Pemain**
- Tidak bisa membuat game baru
- Bisa **memilih dari game yang sudah dibuat developer**
- Tambah game ke **koleksi pribadi**
- Kelola **wishlist** game yang diinginkan
- Lacak **progress** bermain (jam main, persentase selesai)

---

## 🗂️ Database Structure

### Tabel: **game**
| Column | Type | Keterangan |
|--------|------|-----------|
| `id` | INT | Primary Key |
| `developer_id` | INT | Foreign Key ke users (developer pembuat) |
| `judul` | VARCHAR | Nama game |
| `deskripsi` | TEXT | Deskripsi game |
| `kategori_id` | INT | Kategori game (RPG, Action, etc) |
| `developer_name` | VARCHAR | Nama studio/developer |
| `tahun_rilis` | INT | Tahun rilis game |
| `gambar` | VARCHAR | Path gambar cover |
| `status` | VARCHAR | Status: 'tersedia' (default) |
| `rating` | DECIMAL | Rating game (0-10) |
| `tanggal_ditambah` | TIMESTAMP | Waktu dibuat |

### Tabel: **koleksi** (tidak berubah)
- Merekam game mana aja yang user punya di koleksi
- Linked ke `game.id` (bukan ke developer)

### Tabel: **wishlist** (tidak berubah)
- Merekam game mana aja yang user ingin punya

---

## 🔄 Alur Sistem

```
1. Developer mendaftar dengan role "Developer"
   ↓
2. Developer buat game baru (form "Tambah Game Baru" di dashboard)
   ↓
3. Game tersimpan di database dengan developer_id = id developer
   ↓
4. Game status otomatis "tersedia" untuk semua pemain
   ↓
5. Pemain login dengan role "Pemain"
   ↓
6. Pemain lihat dropdown "Pilih Game" (menampilkan semua game dari semua developer)
   ↓
7. Pemain pilih game → Tambah ke koleksi
   ↓
8. Game sekarang masuk ke koleksi pemain
   ↓
9. Pemain bisa kelola koleksi (edit progress, hapus game)
```

---

## 💾 Database Setup

### **Fresh Installation:**
1. Run `setup_database.sql` di phpMyAdmin
2. Database otomatis membuat semua table dan struktur baru

### **Upgrade dari versi lama:**
1. Run `migration_update_game_table.sql` di phpMyAdmin
2. Atau refresh halaman (auto-migration di config.php)

---

## 🧪 Demo Account

```
👤 Pemain:
  Username: demo_pemain
  Password: demo123
  Role: Pemain

🎮 Developer:
  Username: demo_dev
  Password: demo123
  Role: Developer
```

---

## 📝 Testing Checklist

- [ ] Developer bisa akses form "Tambah Game Baru"
- [ ] Developer bisa membuat game dengan nama studio
- [ ] Game muncul di "Pilih Game" untuk pemain
- [ ] Pemain bisa select game dari dropdown
- [ ] Pemain bisa tambah game ke koleksi
- [ ] Game muncul di "Koleksi Saya" pemain
- [ ] Pemain tidak bisa akses form "Tambah Game Baru"
- [ ] Navbar menampilkan role (👤 atau 🎮)

---

## 🔍 Troubleshooting

**Error: Unknown column 'developer_id'**
- Solusi: Refresh halaman, auto-migration akan berjalan

**Game tidak muncul di dropdown pemain**
- Cek apakah developer sudah buat game
- Cek status game di database (harus "tersedia")

**Pemain tidak bisa tambah game ke koleksi**
- Cek apakah game_id valid
- Cek apakah koleksi table sudah punya data

---

## 🛠️ Functions di functions.php

```php
// Developer
getGames($pdo)              // Ambil game yang dibuat developer ini
buatGame($pdo, ...)         // Buat game baru

// Pemain
getAvailableGames($pdo)     // Ambil semua game tersedia
tambahKeKoleksi($pdo, ...)  // Tambah game ke koleksi

// User
getKoleksiUser($pdo)        // Ambil koleksi user
getWishlistUser($pdo)       // Ambil wishlist user

// Auth
isDeveloper()               // Cek apakah user adalah developer
isPemain()                  // Cek apakah user adalah pemain
getUserRole()               // Ambil role user
```

---

## 📚 File yang Terubah

- `setup_database.sql` - Update game table schema
- `migration_update_game_table.sql` - Untuk upgrade
- `includes/config.php` - Auto-migration script
- `includes/functions.php` - Update semua game functions
- `public/index.php` - Conditional form untuk developer/pemain
- `public/koleksi.php` - Update developer field names
- `public/login.php` - Role selector sudah ada

---

Sistem sudah **fully integrated**! 🎉
