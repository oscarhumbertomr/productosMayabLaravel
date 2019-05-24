<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre_largo',200)->unique();
            $table->string('nombre_corto',50);
            $table->unsignedDecimal('existencia',12,4);
            $table->unsignedDecimal('costo_unitario',12,4);
            $table->unsignedBigInteger('unidad_medida_id');
            $table->timestamps();

            $table->foreign('unidad_medida_id')->references('id')->on('unidad_medida');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('materials');
    }
}
