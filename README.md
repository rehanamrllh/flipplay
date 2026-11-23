# FlipPlay Modul 4 — REST API

Demo ini mengembangkan rancangan Modul 3 menjadi REST API Laravel 12 dengan
Eloquent ORM. API mengelola media FlipPlay dan koleksi media milik pengguna
melalui relasi many-to-many.

## Menjalankan project

```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve
```

Database default menggunakan SQLite pada `database/database.sqlite`.

## Endpoint

| Method | Endpoint | Keterangan |
|---|---|---|
| GET | `/api/media?search=one` | Daftar dan pencarian media |
| POST | `/api/media` | Menambahkan media |
| GET | `/api/media/{id}` | Detail media dan pengguna kolektor |
| PATCH | `/api/media/{id}` | Mengubah media |
| DELETE | `/api/media/{id}` | Menghapus media |
| GET | `/api/users/{id}/collection` | Melihat koleksi pengguna |
| POST | `/api/users/{id}/collection/{mediaId}` | Menambahkan media ke koleksi |
| DELETE | `/api/users/{id}/collection/{mediaId}` | Menghapus media dari koleksi |

Contoh body `POST /api/media`:

```json
{
  "title": "Frieren: Beyond Journey's End",
  "type": "Anime",
  "genre": "Adventure",
  "release_year": 2023,
  "episodes": 28
}
```

Validasi akan menolak judul kosong, judul duplikat, tipe selain `Anime` atau
`Manga`, serta nilai tahun dan jumlah episode/bab yang tidak valid. Media Anime
wajib menggunakan `episodes`, sedangkan media Manga wajib menggunakan
`chapters`. Menambahkan media yang sama dua kali ke koleksi pengguna
menghasilkan HTTP 409.

Untuk memeriksa seluruh route:

```bash
php artisan route:list --path=api
```
