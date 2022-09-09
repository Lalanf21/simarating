## Sedikit tentang flow aplikasi
-> Aplikasi ini merupakan aplikasi pemesanan / reservasi ruang
-> Untuk masuk ke sistem, maka user wajib memasukkan "corp code" yang sudah di insert oleh admin
-> Saat user sudah mengetahui "corp code" nya, maka klik daftar
-> User memasukkan data diri dan corp code nya
-> Jika data sesuai dengan yang ada di DB, Maka akan user akan dapat register dan langsung login
-> User bisa memesan ruang untuk meeting sesuai dengan tanggal, sesi dan waktu yang di inginkan
-> Jika kuota ruangan full, atau ruangan tidak tersedia pada tanggal tersebut, maka akan menampilkan alert

## Data master
-> Kelola user
-> Kelola add-on room
-> Kelola company partner

## Running the app
1. clone repository
2. Jalankan Composer intall pada terminal
3. Copy .env.example menjadi .env
4. Ubah file .env sesuai dengan environment system, misal mysql, password db, host, port, dll
5. jalankan php artisan key:generate pada terminal
6. Jalan php artisan migrate:fresh --seed
7. Run aplikasi via terminal dengan menjalankan php artisan serve
