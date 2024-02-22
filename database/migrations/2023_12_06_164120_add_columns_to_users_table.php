<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gender');
            $table->date('dob')->nullable();
            $table->date('joining_date')->nullable();
            $table->string('adhar_number')->nullable();
            $table->string('aadhar_image_path')->nullable();
            $table->string('pan_card_number')->nullable();
            $table->string('pan_image_path')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedInteger('login_pin')->nullable();
            $table->string('profile_path')->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('device_token')->nullable();
            $table->string('employee_id')->nullable();

            $table->foreign('state_id')->references('id')->on('states');
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gender', 'dob', 'joining_date', 'adhar_number', 'aadhar_image_path', 'pan_card_number', 'pan_image_path', 'approved_by', 'address', 'state_id', 'district_id', 'login_pin','profile_path', 'device_type', 'device_token', 'employee_id']);
        });
    }
}
