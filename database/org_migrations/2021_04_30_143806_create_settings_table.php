<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->string('code', 191);
            $table->enum('type', ['BOOLEAN', 'NUMBER', 'DATE', 'TEXT', 'SELECT', 'FILE', 'TEXTAREA']);
            $table->string('description', 250)->nullable();
            $table->string('category', 191)->default('General');
            $table->string('label', 191);
            $table->longText('default_options')->nullable();
            $table->longText('value')->nullable();
            $table->tinyInteger('editable')->default(1);
            $table->tinyInteger('hidden')->default(0);
            $table->text('properties')->nullable();
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
        Schema::dropIfExists('settings');
    }
}
