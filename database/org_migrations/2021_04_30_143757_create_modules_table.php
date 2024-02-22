<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 191)->unique();
            $table->tinyInteger('enabled')->default(0);
            $table->tinyInteger('installed')->default(0);
            $table->string('installed_version', 191)->nullable();
            $table->integer('load_order')->default(0);
            $table->string('provider', 191)->nullable();
            $table->string('folder', 191)->nullable();
            $table->text('properties')->nullable();
            $table->enum('type', ['core', 'module', 'payment']);
            $table->text('notes')->nullable();
            $table->text('license_key')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->unsignedInteger('updated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modules');
    }
}
