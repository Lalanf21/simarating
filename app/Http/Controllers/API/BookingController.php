<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Model\CWS_TRANSAKSI_MITRA_USER;
use DB;

class BookingController extends Controller
{
    public function getBooking($kode_qr)
    {
        $booking = DB::table('CWS_transaksi_mitra_user as a')
            ->select('a.booking_date','a.status','b.email','b.nama','c.nama_perusahaan')
            ->join('CWS_master_mitra_user as b','b.id','a.id_mitra_user')
            ->join('CWS_master_mitra as c','c.id','b.id_mitra')
            ->where('a.booking_date','=', date('Y-m-d') )
            ->where('a.qr_code_string', $kode_qr)
            ->first();

        if ($booking->status == 0) {
            CWS_TRANSAKSI_MITRA_USER::where('qr_code_string', $kode_qr)
                ->update([
                    'status' => 1,
                ]);
            return ResponseFormatter::success($booking,'Data booking berhasil di ambil, check-in berhasil');
        }else if( $booking->status == 1 ){
            return ResponseFormatter::error('','Kode reservasi sudah check-in','404');
        }else{
            return ResponseFormatter::error('','Kode reservasi tidak ditemukan','404');
        }

    }
}
