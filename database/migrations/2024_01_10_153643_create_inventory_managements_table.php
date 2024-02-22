<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_managements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('inventory_id');
            $table->enum('type', ['Added', 'Consumed'])->default('Added');
            $table->integer('quantity');
            $table->string('unit_of_measurement', 100);
            $table->date('date')->nullable();
            $table->bigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_managements');
    }
}
