<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id');
            $table->string('code', 191)->unique('invoices_code_unique');
            $table->string('currency', 191);
            $table->enum('status', ['paid', 'pending', 'failed'])->default('pending');
            $table->dateTime('due_date');
            $table->dateTime('invoice_date');
            $table->decimal('sub_total');
            $table->decimal('total');
            $table->unsignedInteger('user_id')->index('invoices_user_id_foreign');
            $table->unsignedInteger('order_id');
            $table->tinyInteger('is_sent')->nullable()->default(0);
            $table->unsignedInteger('created_by')->nullable()->index('invoices_created_by_index');
            $table->unsignedInteger('updated_by')->nullable()->index('invoices_updated_by_index');
            $table->softDeletes();
            $table->timestamps();
            $table->string('tally_invoice_no', 50)->nullable();
            $table->string('invoicable_type', 100)->default('Corals\Modules\Ecommerce\Models\Order');
            $table->integer('invoicable_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts_invoices');
    }
}
