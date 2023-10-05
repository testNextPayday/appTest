<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type');
            $table->string('entity_type');
            $table->integer('entity_id');
            $table->boolean('read')->default(false);
            $table->integer('responder_id')->nullable();
            $table->string('responder_type')->nullable(); 
            $table->string('permission_level')->nullable(); // the group that gets this notification          
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
        Schema::dropIfExists('group_notifications');
    }
}
