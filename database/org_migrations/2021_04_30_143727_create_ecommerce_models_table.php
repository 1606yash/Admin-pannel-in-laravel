<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_models', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('organization_id')->nullable();
            $table->integer('manufacturer_id')->nullable();
            $table->string('name', 191);
            $table->string('slug', 191);
            $table->string('file', 250)->nullable();
            $table->string('original_name', 250)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->tinyInteger('is_featured')->default(0);
            $table->text('properties')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index('ecommerce_brands_created_by_index');
            $table->unsignedInteger('updated_by')->nullable()->index('ecommerce_brands_updated_by_index');
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
        Schema::dropIfExists('ecommerce_models');
    }
}
