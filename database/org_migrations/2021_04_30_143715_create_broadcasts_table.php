<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBroadcastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('organization_id')->index('fk_organization_id');
            $table->tinyInteger('type')->comment('1=all_buyers,1=specific_buyer,3=buyer_category');
            $table->string('receivers', 250)->nullable();
            $table->string('buyer_category', 250)->nullable();
            $table->text('message');
            $table->tinyInteger('is_active')->default(1);
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
        Schema::dropIfExists('broadcasts');
    }
}
