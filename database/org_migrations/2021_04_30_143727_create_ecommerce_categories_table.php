<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->string('slug', 191);
            $table->text('description')->nullable();
            $table->unsignedInteger('parent_id')->nullable()->default(0);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->tinyInteger('is_featured')->default(0);
            $table->string('external_id', 191)->nullable();
            $table->text('properties')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
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
        Schema::dropIfExists('ecommerce_categories');
    }
}
