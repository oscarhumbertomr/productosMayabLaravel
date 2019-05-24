<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardexMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cardex_material', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('material_id');
            $table->unsignedBigInteger('cardex_tipo_movs_id');
            $table->unsignedDecimal('cantidad_movida',12,4);
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('material');
            $table->foreign('cardex_tipo_movs_id')->references('id')->on('cardex_tipo_mov');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cardex_materials');
    }
}
