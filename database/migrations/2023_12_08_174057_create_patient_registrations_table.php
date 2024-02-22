<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('ambulance_id')->nullable();
            $table->unsignedBigInteger('service_area_id')->nullable();
            $table->string('requester_name');
            $table->string('pickup_address');
            $table->double('pickup_latitude')->nullable()->default(0);
            $table->double('pickup_longitude')->nullable()->default(0);
            $table->double('drop_latitude')->nullable()->default(0);
            $table->double('drop_longitude')->nullable()->default(0);
            $table->string('drop_address')->nullable();
            $table->string('mobile_number');
            $table->string('relation')->nullable();
            $table->string('patient_name')->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender',['Male', 'Female', 'Others'])->nullable();
            $table->text('reason')->nullable();
            $table->string('start_address')->nullable();
            $table->double('start_longitude')->nullable()->default(0);
            $table->double('start_latitude')->nullable()->default(0);
            $table->float('start_meter_reading')->nullable();
            $table->float('pickup_meter_reading')->nullable();
            $table->float('drop_meter_reading')->nullable();
            $table->float('distance_covered')->nullable();
            $table->string('service_duration')->nullable();
            $table->text('reject_reason')->nullable();
            $table->string('patient_status')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->enum('case_status', ['cancelled', 'pending', 'ongoing', 'completed'])->nullable();
            $table->enum('request_status', ['pending', 'accepted', 'rejected'])->nullable(); 
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ambulance_id')->references('id')->on('ambulances')->onDelete('cascade');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->foreign('service_area_id')->references('id')->on('service_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient_registrations');
    }
}
