<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id');
            $table->string('title');
            $table->string('slug');
            $table->longText('meta_keywords')->nullable();
            $table->longText('meta_description')->nullable();
            $table->longText('description')->nullable();
            $table->integer('status')->default(0);
            $table->integer('visiblity')->default(0);
            $table->string('file', 500);
            $table->string('original_name', 500);
            $table->string('image_link', 500)->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();
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
        Schema::dropIfExists('pages');
    }
}
