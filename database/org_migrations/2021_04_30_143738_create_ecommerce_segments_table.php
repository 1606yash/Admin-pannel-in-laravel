<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id');
            $table->string('type', 250)->nullable();
            $table->string('slug', 250)->nullable();
            $table->integer('industry')->default(0);
            $table->string('description', 250)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->dateTime('created_at');
            $table->dateTime('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ecommerce_segments');
    }
}
