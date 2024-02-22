<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('amount', 20);
            $table->text('description')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('sku_code', 191)->nullable();
            $table->unsignedInteger('order_id')->index('ecommerce_order_items_order_id_foreign');
            $table->integer('item_id');
            $table->string('name');
            $table->decimal('price', 20);
            $table->decimal('original_price', 20);
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
        Schema::dropIfExists('ecommerce_order_items');
    }
}
