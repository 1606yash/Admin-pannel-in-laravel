<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterManufacturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_manufacturers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->string('description', 250)->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->string('slug', 191)->unique('categories_slug_unique');
            $table->enum('status', ['active', 'inactive'])->default('active');
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
        Schema::dropIfExists('master_manufacturers');
    }
}
