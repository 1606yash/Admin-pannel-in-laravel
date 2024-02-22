<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('employee_id')->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('middle_name', 250)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('phone_no', 15)->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->date('joining_date', 15)->nullable();
            $table->string('adhar_number')->nullable();
            $table->text('aadhar_image_path')->nullable();
            $table->string('pan_card_number')->nullable();
            $table->text('pan_image_path')->nullable();
            $table->string('address')->nullable();
            $table->text('profile_path')->nullable();
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('role_id')->nullable()->index('role_id');
            $table->unsignedBigInteger('reporting_manager_id')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->tinyInteger('is_verified')->nullable();
            $table->string('reject_remark')->nullable();
            $table->tinyInteger('is_active')->nullable()->default(1);
            $table->string('password')->nullable();
            $table->dateTime('password_updated_at')->nullable();
            $table->string('otp', 10)->nullable();
            $table->integer('login_pin')->nullable();
            $table->dateTime('otp_expiry')->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_token')->nullable();
            $table->string('token', 250)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
