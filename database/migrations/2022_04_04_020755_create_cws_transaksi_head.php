<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCwsTransaksiHead extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CWS_transaksi_head', function (Blueprint $table) {
            $table->id();
            $table->string('transaksi_no',30);
            $table->string('transaksi_date',10);
            $table->string('booking_date',10);
            $table->string('id_mitra',5);
            $table->string('start_time',10)->nullable();
            $table->string('end_time',10)->nullable();
            $table->string('created_by',100)->nullable();
            $table->string('updated_by',100)->nullable();
            $table->string('is_deleted',1)->nullable();
            $table->string('deleted_by',100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('CWS_transaksi_head');
    }
}
