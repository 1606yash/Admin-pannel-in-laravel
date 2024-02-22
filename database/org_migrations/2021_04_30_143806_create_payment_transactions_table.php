<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 191);
            $table->string('owner_type', 191);
            $table->unsignedBigInteger('owner_id');
            $table->unsignedInteger('invoice_id')->nullable()->index('payment_transactions_invoice_id_foreign');
            $table->string('sourcable_type', 191)->nullable();
            $table->unsignedBigInteger('sourcable_id')->nullable();
            $table->double('amount', 8, 2)->default(0.00);
            $table->string('paid_currency', 191)->nullable();
            $table->double('paid_amount', 8, 2)->nullable();
            $table->string('type', 191)->nullable();
            $table->string('method', 191)->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->enum('status', ['completed', 'pending', 'cancelled'])->default('completed');
            $table->text('notes')->nullable();
            $table->text('extra')->nullable();
            $table->string('reference', 191)->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['sourcable_type', 'sourcable_id']);
            $table->index(['owner_type', 'owner_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_transactions');
    }
}
