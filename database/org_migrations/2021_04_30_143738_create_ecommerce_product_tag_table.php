<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceProductTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_product_tag', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('tag_id')->index('ecommerce_product_tag_tag_id_foreign');
            $table->timestamps();
            $table->unique(['product_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ecommerce_product_tag');
    }
}
