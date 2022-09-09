<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuksesBookingEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->booking_date = $data['booking_date'];
        $this->nama = $data['nama'];
        $this->nama_perusahaan = $data['nama_perusahaan'];
        $this->no_transaksi = $data['no_transaksi'];
        $this->qrCode = $data['qrCode'];
    }

    
    public function build()
    {
        return $this->from('admin@pradita.ac.id')
        ->subject('Detail reservation Pradita Partner Lounge')
        ->view('email.mail')
        ->with(
         [
             'booking_date' => $this->booking_date,
             'nama' => $this->nama,
             'nama_perusahaan' => $this->nama_perusahaan,
             'no_transaksi' => $this->no_transaksi,
             'qrCode' => $this->qrCode,
         ]);
    }
}
