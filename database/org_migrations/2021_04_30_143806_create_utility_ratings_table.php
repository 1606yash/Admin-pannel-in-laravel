<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilityRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utility_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('organization_id')->nullable();
            $table->integer('rating');
            $table->string('title', 191)->nullable();
            $table->string('body', 191)->nullable();
            $table->string('reviewrateable_type', 191);
            $table->unsignedBigInteger('reviewrateable_id');
            $table->string('author_type', 191);
            $table->unsignedBigInteger('author_id');
            $table->text('properties')->nullable();
            $table->string('criteria', 191)->nullable();
            $table->enum('status', ['approved', 'disapproved', 'spam', 'pending'])->default('approved');
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['author_type', 'author_id']);
            $table->index(['reviewrateable_type', 'reviewrateable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('utility_ratings');
    }
}
