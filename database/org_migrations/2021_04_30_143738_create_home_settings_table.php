<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id');
            $table->string('categories')->nullable();
            $table->tinyInteger('best_seller')->default(1);
            $table->tinyInteger('featured_product')->default(1);
            $table->tinyInteger('brands')->default(1);
            $table->tinyInteger('models')->default(1);
            $table->tinyInteger('recommended_products')->default(1);
            $table->tinyInteger('new_arrivals')->default(1);
            $table->tinyInteger('inventory')->default(1);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('home_settings');
    }
}
