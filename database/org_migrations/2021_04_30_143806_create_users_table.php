<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('organization_id')->index('fk_organization_id');
            $table->string('name', 191);
            $table->string('last_name', 191)->nullable();
            $table->string('email', 191)->unique();
            $table->string('password', 191);
            $table->string('phone_number', 191)->nullable();
            $table->string('file')->nullable();
            $table->string('original_name')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->string('confirmation_code', 191)->nullable();
            $table->rememberToken();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
            $table->text('shop_name')->nullable();
            $table->text('gst')->nullable();
            $table->integer('status')->default(1);
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->integer('country')->nullable();
            $table->integer('state')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->integer('district')->default(0);
            $table->integer('city')->default(0);
            $table->tinyInteger('is_synced_in_tally')->default(0);
            $table->string('tally_customer_id', 20)->nullable();
            $table->enum('source', ['web', 'tally'])->default('web');
            $table->string('fcm_token', 300)->nullable();
            $table->integer('is_approved')->default(1);
            $table->dateTime('approved_at')->nullable();
            $table->integer('retailer_category')->default(0);
            $table->integer('credit_limit')->default(0);
            $table->decimal('used_limit', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
