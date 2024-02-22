<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('attendance_date');
            $table->time('login_time')->nullable();
            $table->time('logout_time')->nullable();
            $table->float('login_meter_reading')->nullable();
            $table->string('login_meter_photo')->nullable();
            $table->float('logout_meter_reading')->nullable();
            $table->string('logout_meter_photo')->nullable();
            $table->string('station_location')->nullable();
            $table->string('login_location')->nullable();
            $table->double('login_latitude')->nullable();
            $table->double('login_longitude')->nullable();
            $table->string('logout_location')->nullable();
            $table->double('logout_latitude')->nullable();
            $table->double('logout_longitude')->nullable();
            $table->time('duration')->nullable();
            $table->float('km_run')->nullable();
            $table->unsignedBigInteger('shift_type_id')->nullable();
            $table->enum('login_status', ['Present', 'Absent', 'Leave', 'Weekly Off'])->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shift_type_id')->references('id')->on('shift_types')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
