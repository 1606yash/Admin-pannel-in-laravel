<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmbulancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ambulances', function (Blueprint $table) {
            $table->id();
            $table->enum('registration_number_available', ['Yes', 'Not Available'])->nullable();
            $table->string('ambulance_no')->nullable();
            $table->string('make')->nullable();
            $table->string('chassis_no')->nullable();
            $table->enum('status', ['Running', 'Not Running'])->default('Running');            
            $table->string('number_plate_image_path')->nullable();
            $table->string('registration_certificate_path')->nullable();
            $table->string('purchase_paper_path')->nullable();
            $table->string('fastags_image_path')->nullable();
            $table->unsignedBigInteger('district_id');
            $table->date('inauguration_date')->nullable();
            $table->enum('number_plate_available', ['Yes', 'Not Available'])->nullable();
            $table->enum('registration_certificate_available', ['Yes', 'Not Available'])->nullable();
            $table->enum('purchase_paper_available', ['Yes', 'Not Available'])->nullable();
            $table->enum('fastags_available', ['Yes', 'Not Available'])->nullable();
            $table->string('sponsor_name')->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->text('additional_notes')->nullable();
            $table->date('date_of_delivery')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('entry_date')->nullable();
            $table->string('station_location')->nullable();
            $table->string('service_location')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('district_id')->references('id')->on('districts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ambulances');
    }
}
