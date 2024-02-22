<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSliderOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slider_options', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 191);
            $table->string('default', 191);
            $table->string('type', 191);
            $table->text('values');
            $table->text('description');
            $table->string('slider_type', 191)->default('OwlCarousel2');
            $table->tinyInteger('hidden')->default(0);
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
        Schema::dropIfExists('slider_options');
    }
}
