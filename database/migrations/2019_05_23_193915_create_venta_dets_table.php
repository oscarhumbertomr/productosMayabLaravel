<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVentaDetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venta_dets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('venta_id');
            $table->unsignedBigInteger('material_id');

            $table->unsignedDecimal('cantidad',12,4);
            $table->unsignedDecimal('importe',12,4)->comment('presio unitario del material por el la cantidad vendidas');

            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('material');
            $table->foreign('venta_id')->references('id')->on('venta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('venta_dets');
    }
}
