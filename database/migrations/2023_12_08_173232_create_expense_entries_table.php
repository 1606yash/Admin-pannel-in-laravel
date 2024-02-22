<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ambulance_id')->nullable();
            $table->unsignedBigInteger('expense_type_id')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->date('expense_date')->nullable();
            $table->string('entry_type')->nullable();
            $table->string('bill_photo_path')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->float('km_reading')->nullable();
            $table->enum('claim_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->date('claim_date')->nullable();
            $table->string('non_vendor')->nullable();
            $table->string('fuel_type')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->enum('reimbursement_status', ['Pending', 'Approved', 'Rejected', 'Completed'])->default('Pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ambulance_id')->references('id')->on('ambulances')->onDelete('cascade');
            $table->foreign('expense_type_id')->references('id')->on('expense_types')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('expense_entries');
    }
}
