<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterNotificationTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_notification_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->string('event_name', 191);
            $table->string('friendly_name', 191);
            $table->string('title', 191)->nullable();
            $table->text('body')->nullable();
            $table->text('extras')->nullable();
            $table->text('via')->nullable();
            $table->unsignedInteger('updated_by')->nullable()->index('notification_templates_updated_by_index');
            $table->unsignedInteger('created_by')->nullable()->index('notification_templates_created_by_index');
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
        Schema::dropIfExists('master_notification_templates');
    }
}
