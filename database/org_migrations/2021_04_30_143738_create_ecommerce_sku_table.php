<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceSkuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_sku', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('regular_price');
            $table->decimal('sale_price')->nullable();
            $table->string('code', 191);
            $table->enum('inventory', ['finite', 'bucket', 'infinite'])->nullable()->default('infinite');
            $table->string('inventory_value', 191)->nullable();
            $table->unsignedInteger('product_id')->index();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('shipping')->nullable();
            $table->integer('allowed_quantity')->default(0);
            $table->text('properties')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
            $table->integer('moq')->default(1);
            $table->string('hsn', 100)->nullable();
            $table->dateTime('expiry_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ecommerce_sku');
    }
}
