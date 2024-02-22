<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('organization_id');
            $table->integer('buyer')->default(0);
            $table->integer('dsp');
            $table->enum('plan_type', ['ONFIELD', 'OFFFIELD'])->default('ONFIELD');
            $table->enum('plan', ['BUYER_VISIT', 'FULL_DAY_LEAVE', 'HALF_DAY_LEAVE', 'HOLIDAY', 'HO'])->default('BUYER_VISIT');
            $table->text('plan_comment')->nullable();
            $table->date('planned_date');
            $table->dateTime('checked_in_at')->nullable();
            $table->dateTime('checked_out_at')->nullable();
            $table->tinyInteger('is_system_checkout')->default(0);
            $table->text('checkout_comment')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->tinyInteger('is_system_cancelled')->default(0);
            $table->text('cancel_comment')->nullable();
            $table->string('lat', 50)->nullable();
            $table->string('lng', 50)->nullable();
            $table->integer('created_by');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();
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
        Schema::dropIfExists('visits');
    }
}
