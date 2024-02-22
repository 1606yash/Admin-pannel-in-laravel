<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmbulanceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ambulance_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ambulance_id');
            $table->string('policy_company')->nullable();
            $table->string('policy_number')->nullable();
            $table->enum('insurance_available', ['Yes', 'Not Available'])->nullable();
            $table->date('insurance_start_date')->nullable();
            $table->date('insurance_valid_upto')->nullable();
            $table->string('insurance_upload_path')->nullable();
            $table->enum('puc_available', ['Yes', 'Not Available'])->nullable();
            $table->date('puc_certificate_validity')->nullable();
            $table->string('puc_certificates_path')->nullable();
            $table->enum('fitness_available', ['Yes', 'Not Available'])->nullable();
            $table->date('fitness_certificate_validity')->nullable();
            $table->string('fitness_certificate_upload_path')->nullable();
            $table->string('supplier_name')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->date('payment_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ambulance_id')->references('id')->on('ambulances');
            $table->foreign('bank_id')->references('id')->on('bank_lists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ambulance_details');
    }
}
