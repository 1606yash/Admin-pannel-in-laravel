<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDefaultSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('default_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 191)->unique('settings_code_unique');
            $table->enum('type', ['BOOLEAN', 'NUMBER', 'DATE', 'TEXT', 'SELECT', 'FILE', 'TEXTAREA']);
            $table->string('description', 250)->nullable();
            $table->string('category', 191)->default('General');
            $table->string('label', 191);
            $table->longText('value')->nullable();
            $table->tinyInteger('editable')->default(1);
            $table->tinyInteger('hidden')->default(0);
            $table->text('properties')->nullable();
            $table->unsignedInteger('created_by')->nullable()->index('settings_created_by_index');
            $table->unsignedInteger('updated_by')->nullable()->index('settings_updated_by_index');
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
        Schema::dropIfExists('default_settings');
    }
}
