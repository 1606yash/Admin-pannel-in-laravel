<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 191)->unique();
            $table->string('currency', 191);
            $table->text('description')->nullable();
            $table->text('terms')->nullable();
            $table->enum('status', ['paid', 'pending', 'failed'])->default('pending');
            $table->dateTime('due_date');
            $table->dateTime('invoice_date');
            $table->decimal('sub_total');
            $table->decimal('order_total')->default(0.00);
            $table->decimal('total');
            $table->unsignedInteger('user_id')->index('invoices_user_id_foreign');
            $table->string('invoicable_type', 191);
            $table->unsignedInteger('invoicable_id');
            $table->tinyInteger('is_sent')->nullable()->default(0);
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
            $table->text('properties')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->string('tally_invoice_no', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
