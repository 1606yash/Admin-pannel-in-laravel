<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHttpLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('http_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip', 191)->nullable()->index();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('email', 191)->nullable()->index();
            $table->string('uri', 191)->nullable()->index();
            $table->string('method', 191)->nullable();
            $table->text('headers')->nullable();
            $table->text('body')->nullable();
            $table->text('response')->nullable();
            $table->text('files')->nullable();
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
        Schema::dropIfExists('http_log');
    }
}
