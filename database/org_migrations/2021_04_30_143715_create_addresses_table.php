<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->tinyInteger('address_type')->comment('1 = Billing, 2 = Shipping ');
            $table->tinyInteger('is_default')->default(0);
            $table->string('name');
            $table->string('address1');
            $table->string('address2')->nullable();
            $table->integer('country');
            $table->integer('state');
            $table->integer('district');
            $table->integer('city');
            $table->string('pincode', 6);
            $table->tinyInteger('is_active')->default(1);
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();
            $table->integer('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
