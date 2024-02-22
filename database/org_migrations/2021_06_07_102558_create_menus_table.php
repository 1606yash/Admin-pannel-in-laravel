<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id')->default(0);
            $table->enum('menu_type', ['Side-Menu', 'Desktop', 'Mobile-Footer']);
            $table->string('url', 191)->nullable();
            $table->text('icon')->nullable();
            $table->integer('role_id')->default(0);
            $table->string('name', 191)->nullable();
            $table->text('description')->nullable();
            $table->enum('target', ['_blank', '_self'])->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->tinyInteger('is_default')->default(0);
            $table->integer('order')->default(0);
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
        Schema::dropIfExists('menus');
    }
}
