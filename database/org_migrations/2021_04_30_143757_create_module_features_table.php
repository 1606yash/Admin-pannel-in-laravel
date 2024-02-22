<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModuleFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_features', function (Blueprint $table) {
            $table->integer('id', true);
            $table->enum('module', ['', 'Users', 'Business', 'Ecommerce', 'Administration', 'Reports', 'CMS', 'Broadcast', 'Reports', 'Contact-Us'])->nullable();
            $table->string('feature', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_features');
    }
}
