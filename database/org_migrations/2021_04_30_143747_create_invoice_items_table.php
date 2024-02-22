<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 191)->unique();
            $table->decimal('amount');
            $table->integer('quantity')->nullable()->default(1);
            $table->string('itemable_type', 191);
            $table->unsignedInteger('itemable_id');
            $table->string('sku_code', 100)->nullable();
            $table->string('object_reference', 191)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('invoice_id')->index('invoice_items_invoice_id_foreign');
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
            $table->tinyInteger('is_invoiced')->default(0);
            $table->integer('quantity_invoiced')->default(0);
            $table->decimal('amount_invoiced', 20)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_items');
    }
}
