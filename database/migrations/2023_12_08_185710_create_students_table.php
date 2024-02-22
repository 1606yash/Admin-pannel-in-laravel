<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('gender');
            $table->date('dob');
            $table->string('class');
            $table->string('nationality')->nullable();
            $table->string('category')->nullable();
            $table->string('center')->nullable();
            $table->string('medium_of_examination')->nullable();
            $table->string('identification_mark')->nullable();
            $table->string('disability')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('address')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('previous_class_document_path')->nullable();
            $table->string('birth_certificate_path')->nullable();
            $table->string('address_proof_path')->nullable();
            $table->string('id_proof_path')->nullable();
            $table->string('application_form_path')->nullable();
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
        Schema::dropIfExists('students');
    }
}
