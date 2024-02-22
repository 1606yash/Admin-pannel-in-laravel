<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('targets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('organization_id');
            $table->integer('user_id');
            $table->integer('role_id');
            $table->integer('month');
            $table->integer('year');
            $table->decimal('total_sales', 10);
            $table->decimal('achieved_sales', 10);
            $table->integer('total_line_items');
            $table->integer('achieved_line_items');
            $table->integer('total_orders');
            $table->integer('achieved_orders');
            $table->dateTime('created_at')->useCurrent();
            $table->dateTime('updated_at')->nullable();
            $table->softDeletes();
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('targets');
    }
}
