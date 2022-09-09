<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCwsMasterMitra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CWS_master_mitra', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan',100);
            $table->string('nama_brand',100);
            $table->string('corporation_code',10);
            $table->string('kuota',3);
            $table->string('logo',100);
            $table->string('pic',3)->nullable();
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
        Schema::dropIfExists('CWS_master_mitra');
    }
}
