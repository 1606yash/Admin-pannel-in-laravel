<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterProductSkuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_product_sku', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('regular_price');
            $table->decimal('sale_price')->nullable();
            $table->string('code', 191);
            $table->enum('inventory', ['finite', 'bucket', 'infinite'])->nullable()->default('infinite');
            $table->string('inventory_value', 191)->nullable();
            $table->unsignedInteger('product_id')->index('ecommerce_sku_product_id_index');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('shipping')->nullable();
            $table->integer('allowed_quantity')->default(0);
            $table->text('properties')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index('ecommerce_sku_created_by_index');
            $table->unsignedInteger('updated_by')->nullable()->index('ecommerce_sku_updated_by_index');
            $table->softDeletes();
            $table->timestamps();
            $table->integer('moq')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_product_sku');
    }
}
