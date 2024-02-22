<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id');
            $table->integer('feature_id')->index('na_delete_permission');
            $table->integer('read_own')->default(0);
            $table->tinyInteger('read_all')->default(0);
            $table->tinyInteger('edit_own')->default(0);
            $table->tinyInteger('edit_all')->default(0);
            $table->tinyInteger('delete_own')->default(0);
            $table->tinyInteger('delete_all')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->dateTime('created_at')->useCurrent();
            $table->integer('created_by');
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('organization_permissions');
    }
}
