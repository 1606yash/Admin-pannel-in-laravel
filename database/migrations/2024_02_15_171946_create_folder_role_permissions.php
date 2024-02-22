<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFolderRolePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('folder_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id')->index('role_id');
            $table->integer('folder_id')->index('folder_id');
            $table->integer('permission_category_id')->index('permission_category_id');
            $table->integer('created_by');
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('folder_role_permissions');
    }
}
