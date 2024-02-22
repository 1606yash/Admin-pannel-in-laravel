<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id');
            $table->string('order_number', 191);
            $table->decimal('amount', 20);
            $table->string('currency', 191);
            $table->string('status', 191);
            $table->text('shipping')->nullable();
            $table->text('billing')->nullable();
            $table->unsignedInteger('user_id')->nullable()->index('ecommerce_orders_user_id_foreign');
            $table->text('properties')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
            $table->tinyInteger('is_synced_in_tally')->default(0);
            $table->string('tally_status', 100)->nullable();
            $table->string('tally_voucher_no', 100)->nullable();
            $table->dateTime('tally_voucher_date')->nullable();
            $table->dateTime('tally_voucher_import_time')->nullable();
            $table->enum('source', ['web', 'tally'])->default('web');
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ecommerce_orders');
    }
}
