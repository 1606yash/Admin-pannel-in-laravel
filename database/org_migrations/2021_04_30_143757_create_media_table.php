<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->increments('id');
            $table->string('model_type', 191);
            $table->unsignedBigInteger('model_id');
            $table->string('collection_name', 191);
            $table->string('name', 191);
            $table->string('file_name', 191);
            $table->string('mime_type', 191)->nullable();
            $table->string('disk', 191);
            $table->unsignedInteger('size');
            $table->longText('manipulations');
            $table->longText('custom_properties');
            $table->text('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
            $table->timestamps();
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
}
