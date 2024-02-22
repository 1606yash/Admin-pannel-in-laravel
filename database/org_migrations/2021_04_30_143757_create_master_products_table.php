<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->string('type', 191);
            $table->string('slug', 191)->unique('ecommerce_products_slug_unique');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');
            $table->integer('manufacturer_id')->nullable();
            $table->unsignedInteger('brand_id')->nullable()->index('ecommerce_products_brand_id_index');
            $table->text('properties')->nullable();
            $table->text('shipping')->nullable();
            $table->text('caption')->nullable();
            $table->string('code', 191)->nullable();
            $table->tinyInteger('is_featured')->default(0);
            $table->text('external_url')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index('ecommerce_products_created_by_index');
            $table->unsignedInteger('updated_by')->nullable()->index('ecommerce_products_updated_by_index');
            $table->softDeletes();
            $table->timestamps();
            $table->integer('model_id')->nullable();
            $table->tinyInteger('is_synced_in_tally')->default(0);
            $table->enum('source', ['web', 'tally'])->default('web');
            $table->tinyInteger('is_to_be_promoted')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_products');
    }
}
