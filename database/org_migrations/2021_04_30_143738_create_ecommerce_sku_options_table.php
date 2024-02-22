<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceSkuOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_sku_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('sku_id')->index();
            $table->unsignedInteger('attribute_id')->nullable()->index();
            $table->unsignedInteger('attribute_option_id')->nullable()->index();
            $table->string('string_value')->nullable();
            $table->double('number_value')->nullable();
            $table->text('text_value')->nullable();
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
        Schema::dropIfExists('ecommerce_sku_options');
    }
}
