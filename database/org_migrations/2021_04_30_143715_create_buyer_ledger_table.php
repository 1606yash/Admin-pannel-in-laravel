<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuyerLedgerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buyer_ledger', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('organization_id');
            $table->integer('user_id');
            $table->string('invoice_number', 100);
            $table->decimal('amount', 20);
            $table->enum('transaction_type', ['CR', 'DR']);
            $table->dateTime('transaction_date');
            $table->decimal('outstanding', 20)->default(0.00);
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->string('tally_id', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buyer_ledger');
    }
}
