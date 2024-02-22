<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmbulanceShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ambulance_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ambulance_mapping_id');
            $table->enum('user_type',['Driver', 'Attendant'])->nullable();
            $table->enum('type', ['Permanent', 'Temporary'])->nullable();
            $table->unsignedBigInteger('service_area_id');
            $table->string('station_area');
            $table->time('start_time');
            $table->time('end_time');
            $table->date('date');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ambulance_mapping_id')->references('id')->on('ambulance_user_mappings');
            $table->foreign('service_area_id')->references('id')->on('service_areas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ambulance_shifts');
    }
}
