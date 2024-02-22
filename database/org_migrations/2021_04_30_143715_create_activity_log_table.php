<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('log_name', 191)->nullable()->index();
            $table->longText('description');
            $table->integer('subject_id')->nullable()->index();
            $table->string('subject_type', 191)->nullable()->index();
            $table->integer('causer_id')->nullable()->index();
            $table->string('causer_type', 191)->nullable()->index();
            $table->text('properties')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_log');
    }
}
