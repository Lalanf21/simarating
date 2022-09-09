<?php

use Illuminate\Support\Facades\Route;

// auth
Route::get('/', 'Auth\AuthController@index')->name('login');
Route::post('/proses_login', 'Auth\AuthController@proses_login')->name('proses_login');
Route::post('/proses_register', 'Auth\AuthController@proses_register')->name('proses_register');
Route::get('/register', 'Auth\AuthController@register')->name('register');
Route::post('/get-mitra-perusahaan','Auth\AuthController@getMitraPerusahaan')->name('get_mitra_perusahaan');
Route::get('users/verifikasi/{token}', 'Auth\AuthController@verifikasi')->name('verifikasi'); 
Route::get('/logout', 'Auth\AuthController@logout')->name('logout');

Route::middleware('auth','verifikasi')->group(function(){
    // dashboard
    Route::post('/master-data/get_corporate_code','Admin\Master\UserCoWorkingController@getCorporateCode')->name('get_corporate_code');
    Route::get('/dashboard', 'Dashboard\DashboardController@index')->name('dashboard');
    
    // cek sisa sear
    Route::post('/cek-kuota','Pengguna\Booking\BookingController@cekAllData')->name('cek_data');


    Route::post('/transaksi/get-addon-time', 'Pengguna\Booking\BookingController@getAddonTime')->name('getAddonTime');
    
    // ubah sandi
    Route::post('/proses-ubah-password','Admin\Setting\UsersController@proses_ubah_password')->name('proses_ubah_password');
    
    // view profile
    Route::get('/view-profile','Admin\Setting\UsersController@view_profile')->name('view_profile');
});

// Level 1: admin, level 2: pengguna
Route::middleware(['auth', 'verifikasi','level:1'])->group(function(){
    // mitra
    Route::get('/master-data/mitra', 'Admin\Master\MitraController@index')->name('master-mitra');
    Route::post('/master-data/search_mitra','Admin\Master\MitraController@SearchData')->name('search_mitra');
    Route::post('/master-data/add_mitra','Admin\Master\MitraController@store')->name('add-mitra');
    Route::post('/master-data/update_mitra','Admin\Master\MitraController@update')->name('edit-mitra');
    Route::post('/master-data/delete_mitra','Admin\Master\MitraController@destroy')->name('delete-mitra');
    
    // addon room
    Route::get('/master-data/addon_room', 'Admin\Master\AddonController@index')->name('master-addon');
    Route::post('/master-data/search_addon_room','Admin\Master\AddonController@SearchData')->name('search_addon_room');
    Route::post('/master-data/add_addon_room','Admin\Master\AddonController@store')->name('add_addon_room');
    Route::post('/master-data/update_addon_room','Admin\Master\AddonController@update')->name('edit_addon_room');
    Route::post('/master-data/delete_addon_room','Admin\Master\AddonController@destroy')->name('delete_addon_room');
    
    
    // user co-working
    Route::get('/master-data/user_co_working', 'Admin\Master\UserCoWorkingController@index')->name('master_user_co_working');
    Route::post('/master-data/search_co_working','Admin\Master\UserCoWorkingController@SearchData')->name('search_user_co_working');
    Route::post('/master-data/add_co_working','Admin\Master\UserCoWorkingController@store')->name('add_user_co_working');
    Route::post('/master-data/update_co_working','Admin\Master\UserCoWorkingController@update')->name('edit_user_co_working');
    Route::post('/master-data/delete_co_working','Admin\Master\UserCoWorkingController@destroy')->name('delete_user_co_working');
    
    
    // setting : kapasitas
    Route::get('/setting/kapasitas', 'Admin\Setting\KapasitasController@index')->name('setting-kapasitas');
    Route::post('/setting/kapasitas','Admin\Setting\KapasitasController@store')->name('add_kapasitas');
    
    // setting : users
    Route::get('/setting/users', 'Admin\Setting\UsersController@index')->name('setting-users');
    Route::post('/setting/search_users','Admin\Setting\UsersController@SearchData')->name('search_users');
    Route::post('/setting/add_users', 'Admin\Setting\UsersController@store')->name('add_users');
    Route::post('/setting/update_users','Admin\Setting\UsersController@update')->name('edit_users');
    Route::post('/setting/delete_users', 'Admin\Setting\UsersController@destroy')->name('delete_users');
    
    // transaksi : scheduleController
    Route::get('/transaksi/reservasi', 'Admin\Transaksi\ScheduleController@index')->name('schedule');
    Route::post('/transaksi/search_reservasi','Admin\Transaksi\ScheduleController@SearchData')->name('search_schedule');
    Route::get('/transaksi/add_reservasi','Admin\Transaksi\ScheduleController@create')->name('add_schedule');
    Route::post('/transaksi/get_perusahaan_code','Admin\Transaksi\ScheduleController@getPerusahaan')->name('get_perusahaan_code');
    Route::post('/transaksi/get_list_mitra','Admin\Transaksi\ScheduleController@getListMitraById')->name('get_list_mitra');
    Route::post('/transaksi/confirmation-reservasi', 'Admin\Transaksi\ScheduleController@confirmation')->name('confirmation-schedule');
    Route::post('/transaksi/proses-add-reservasi', 'Admin\Transaksi\ScheduleController@store')->name('proses-add-schedule');
    Route::post('/transaksi/detail-data-reservasi', 'Admin\Transaksi\ScheduleController@detailDataSchedule')->name('detail_data_schedule');
    Route::post('/transaksi/delete-reservasi', 'Admin\Transaksi\ScheduleController@destroy')->name('delete_schedule');
   
    
    // transaksi : dashboardController
    Route::post('/transaksi/getAddonTimeline', 'Dashboard\DashboardController@getAddonTimeline')->name('getAddonTimeline');
    Route::post('/transaksi/getChartBooking', 'Dashboard\DashboardController@getChartBooking1')->name('getChartBooking');
    Route::post('/transaksi/getChartKuotaTerpakai', 'Dashboard\DashboardController@getChartKuotaTerpakai')->name('getChartKuotaTerpakai');

    // Laporan user co-working
    Route::get('/laporan/user_co_working', 'Admin\Laporan\LaporanUserCoWorkingController@index')->name('laporan_user_co_working');
    Route::post('/laporan/user_co_working/export-excel', 'Admin\Laporan\LaporanUserCoWorkingController@exportExcel')->name('export_excel_user_co_working');

    // Laporan booking
    Route::get('/laporan/booking', 'Admin\Laporan\LaporanBookingController@index')->name('laporan_booking');
    Route::post('/laporan/booking/export-excel', 'Admin\Laporan\LaporanBookingController@exportExcel')->name('export_excel_booking');
    
    // time table
    Route::get('/dashboard/time-table', 'Dashboard\DashboardController@timeTable')->name('time_table');
});

Route::middleware(['auth','verifikasi', 'level:2'])->group(function(){
    // booking
    Route::get('/user/reservation', 'Pengguna\Booking\BookingController@index')->name('user-booking');
    Route::post('/user/confirmation-reservasi', 'Pengguna\Booking\BookingController@confirmation')->name('confirmation-booking');
    Route::post('/user/proses-reservasi', 'Pengguna\Booking\BookingController@store')->name('proses-booking');
    Route::get('/user/history-reservation', 'Pengguna\Booking\BookingController@riwayat_booking')->name('riwayat-booking');
    Route::post('/user/search-reservasi','Pengguna\Booking\BookingController@search_riwayat_booking')->name('search_booking');
    Route::get('/user/reservation/{transaksi_no}/success','Pengguna\Booking\BookingController@success_booking')->name('success_booking');    
});
// Route::get('/testmail',function(){
//     return view('email.verifikasi_email');
// });