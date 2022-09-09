## Sedikit tentang flow aplikasi
-> Aplikasi ini merupakan aplikasi pemesanan / reservasi ruang <br>
-> Untuk masuk ke sistem, maka user wajib memasukkan "corp code" yang sudah di insert oleh admin <br>
-> Saat user sudah mengetahui "corp code" nya, maka klik daftar <br>
-> User memasukkan data diri dan corp code nya <br>
-> Jika data sesuai dengan yang ada di DB, Maka akan user akan dapat register dan langsung login <br>
-> User bisa memesan ruang untuk meeting sesuai dengan tanggal, sesi dan waktu yang di inginkan <br>
-> Jika kuota ruangan full, atau ruangan tidak tersedia pada tanggal tersebut, maka akan menampilkan alert <br>

## Data master
-> Kelola user <br>
-> Kelola add-on room <br>
-> Kelola company partner <br>

## Running the app
<ol>
    <li> clone repository </li>
    <li> Jalankan Composer intall pada terminal </li>
    <li> Copy .env.example menjadi .env </li>
    <li> Ubah file .env sesuai dengan environment system, misal mysql, password db, host, port, dll </li>
    <li> jalankan php artisan key:generate pada terminal </li>
    <li> Jalan php artisan migrate:fresh --seed </li>
    <li> Run aplikasi via terminal dengan menjalankan php artisan serve </li>
</ol>
