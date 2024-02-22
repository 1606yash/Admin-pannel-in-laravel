<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterProductMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_product_media', function (Blueprint $table) {
            $table->unsignedInteger('product_id')->index('fk_product_id');
            $table->string('file', 250)->nullable();
            $table->string('original_name', 250)->nullable();
            $table->tinyInteger('attachment_type')->nullable()->comment('1=image,2=doc');
            $table->tinyInteger('type')->nullable()->comment('1=main,2=other,3=thumb,4=desktop');
            $table->string('updated_at', 250);
            $table->string('created_at', 250);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_product_media');
    }
}
