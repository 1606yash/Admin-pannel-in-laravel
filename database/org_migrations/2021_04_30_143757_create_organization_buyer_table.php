<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationBuyerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_buyer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id');
            $table->integer('buyer_id');
            $table->integer('buyer_category');
            $table->decimal('credit_limit', 20)->default(0.00);
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('is_synced_in_tally')->default(0);
            $table->string('tally_customer_id')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organization_buyer');
    }
}
