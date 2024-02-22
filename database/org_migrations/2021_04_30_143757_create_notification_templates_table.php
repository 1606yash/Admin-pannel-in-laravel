<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('organization_id')->nullable();
            $table->string('name', 191);
            $table->string('event_name', 191);
            $table->string('friendly_name', 191);
            $table->string('title', 191)->nullable();
            $table->text('email_subject')->nullable();
            $table->text('body')->nullable();
            $table->text('extras')->nullable();
            $table->text('via')->nullable();
            $table->unsignedInteger('updated_by')->nullable()->index();
            $table->unsignedInteger('created_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();
            $table->text('shortcodes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_templates');
    }
}
