<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->string('name', 191)->unique('categories_name_unique');
            $table->string('description', 250)->nullable();
            $table->string('slug', 191);
            $table->string('belongs_to', 191)->default('post');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('properties')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index('categories_created_by_index');
            $table->unsignedInteger('updated_by')->nullable()->index('categories_updated_by_index');
            $table->softDeletes();
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
        Schema::dropIfExists('master_categories');
    }
}
