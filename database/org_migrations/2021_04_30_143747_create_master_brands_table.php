<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_brands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191)->unique('ecommerce_brands_name_unique');
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
        Schema::dropIfExists('master_brands');
    }
}
