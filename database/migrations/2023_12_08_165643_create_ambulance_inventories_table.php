<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmbulanceInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ambulance_inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ambulance_id');
            $table->string('name');
            $table->string('unit_of_measurement');
            $table->integer('capacity');
            $table->integer('quantity');
            $table->enum('status', ['Added', 'Consumed'])->default('Added');
            $table->date('date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ambulance_id')->references('id')->on('ambulances')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ambulance_inventories');
    }
}
