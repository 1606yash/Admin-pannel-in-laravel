<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('item_guid', 60)->nullable();
            $table->integer('parent_id')->default(0);
            $table->string('name', 191)->nullable();
            $table->string('owner_name')->nullable();
            $table->string('tin', 50)->nullable();
            $table->string('industry', 250)->nullable();
            $table->string('street_1', 500)->nullable();
            $table->string('street_2', 500)->nullable();
            $table->integer('pincode')->nullable();
            $table->integer('city')->nullable();
            $table->integer('district')->default(0);
            $table->integer('state')->nullable();
            $table->integer('country')->nullable();
            $table->string('phone_code', 10)->nullable();
            $table->string('mobile', 250)->nullable();
            $table->string('gst', 50)->nullable();
            $table->enum('status', ['active', 'inactive'])->nullable()->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('whatsapp_notification')->default(0)->comment('0=no,1=yes');
            $table->string('authkey', 250)->nullable();
            $table->integer('currency')->nullable();
            $table->enum('installation_type', ['SINGLE', 'SAAS'])->default('SINGLE');
            $table->enum('organization_type', ['SINGLE', 'MULTIPLE'])->default('SINGLE');
            $table->string('domain')->nullable();
            $table->integer('staff_limit')->default(0);
            $table->integer('seller_limit')->default(0);
            $table->integer('buyer_limit')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
}
