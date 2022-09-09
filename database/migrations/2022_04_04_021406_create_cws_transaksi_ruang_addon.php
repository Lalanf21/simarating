<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCwsTransaksiRuangAddon extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CWS_transaksi_ruang_addon', function (Blueprint $table) {
            $table->id();
            $table->string('id_transaksi_head',30);
            $table->string('id_ruang_addon',5);
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
        Schema::dropIfExists('CWS_transaksi_ruang_addon');
    }
}
