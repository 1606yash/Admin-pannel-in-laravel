<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 191)->index();
            $table->integer('model_id');
            $table->string('model_type', 191);
            $table->text('properties')->nullable();
            $table->enum('cast_type', ['string', 'integer', 'boolean', 'float'])->default('string');
            $table->text('value')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
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
        Schema::dropIfExists('model_settings');
    }
}
