<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->string('name', 191);
            $table->string('description', 250)->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->string('slug', 191);
            $table->string('belongs_to', 191)->default('post');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('file', 250)->nullable();
            $table->string('original_name', 250)->nullable();
            $table->text('properties')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
            $table->tinyInteger('is_featured')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
