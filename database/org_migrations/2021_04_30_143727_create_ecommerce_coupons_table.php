<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 191)->unique('code');
            $table->enum('type', ['fixed', 'percentage'])->default('fixed');
            $table->integer('uses')->nullable();
            $table->decimal('min_cart_total')->nullable();
            $table->decimal('max_discount_value')->nullable();
            $table->string('value', 191);
            $table->dateTime('start')->nullable();
            $table->dateTime('expiry')->nullable();
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
        Schema::dropIfExists('ecommerce_coupons');
    }
}
