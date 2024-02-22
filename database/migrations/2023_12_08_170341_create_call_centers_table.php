<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCallCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_centers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('village_id');
            $table->unsignedBigInteger('district_id');
            $table->unsignedBigInteger('state_id');
            $table->string('office_address');
            $table->unsignedBigInteger('district_anchor_id')->nullable();
            $table->unsignedBigInteger('cluster_coordinator_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('legal_document_path')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('village_id')->references('id')->on('villages')->onDelete('cascade');
            $table->foreign('district_id')->references('id')->on('districts')->onDelete('cascade');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->foreign('district_anchor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cluster_coordinator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('call_centers');
    }
}
