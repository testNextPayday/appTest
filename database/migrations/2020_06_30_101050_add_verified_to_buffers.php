<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVerifiedToBuffers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_buffers', function (Blueprint $table) {
            //
            $table->boolean('verified')->default(false);
            $table->text('dump')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_buffers', function (Blueprint $table) {
            //
            $table->dropColumns(['verified','dump']);
        });
    }
}
