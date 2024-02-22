<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalarySlips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('salary_date');
            $table->tinyInteger('month');
            $table->integer('year');
            $table->decimal('basic_salary');
            $table->decimal('house_rent_allowance');
            $table->decimal('conveyance_allowance');
            $table->decimal('special_allowances');
            $table->decimal('professional_tax');
            $table->decimal('gross_salary');
            $table->decimal('net_payable_amount');
            $table->decimal('pf_contribution');
            $table->timestamps();
            $table->softDeletes();
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_slips');
    }
}
