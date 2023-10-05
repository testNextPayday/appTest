<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Backups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('backups', function(Blueprint $table){
            $table->increments('id');
            $table->string('name');
            $table->text('url');
            $table->string('storage'); // basically s3
            $table->string('backup_frequency'); // daily weekly monthly and yearly
            $table->timestamps();
            $table->softDeletes();
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('backups');
    }
}
