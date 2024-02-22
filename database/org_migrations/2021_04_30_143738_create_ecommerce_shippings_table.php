<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceShippingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_shippings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->integer('priority');
            $table->string('shipping_method', 191);
            $table->decimal('rate')->default(0.00);
            $table->decimal('min_order_total')->default(0.00);
            $table->tinyInteger('exclusive')->default(0);
            $table->string('country', 191)->nullable();
            $table->text('description')->nullable();
            $table->text('properties')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
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
        Schema::dropIfExists('ecommerce_shippings');
    }
}
