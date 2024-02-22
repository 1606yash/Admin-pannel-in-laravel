<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id',20);
            $table->bigInteger('related_resource_id',20)->nullable();
            $table->bigInteger('related_resource_user_id', 20)->nullable();
            $table->string('related_resource_type', 250)->nullable();
            $table->string('notification_title', 250)->nullable();
            $table->string('notification_description', 250)->nullable();
            $table->bigInteger('created_by',20)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->onUpdate(current_timestamp());
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
