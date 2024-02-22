<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_collection', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('organization_id');
            $table->integer('retailer_id');
            $table->integer('collected_by');
            $table->decimal('amount', 20)->default(0.00);
            $table->integer('is_cash')->default(0);
            $table->string('cheque_number', 50)->nullable();
            $table->string('bank_name')->nullable();
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_collection');
    }
}
