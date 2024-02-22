<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilityWishlistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utility_wishlists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id')->default(0);
            $table->unsignedInteger('user_id')->index('utility_wishlists_user_id_foreign');
            $table->unsignedInteger('wishlistable_id');
            $table->string('wishlistable_type', 191);
            $table->text('properties')->nullable();
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
        Schema::dropIfExists('utility_wishlists');
    }
}
