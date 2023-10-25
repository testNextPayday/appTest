<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('virtual_accounts', function (Blueprint $table) {
            $table->id();
            $table->increments('id');
            $table->bigInteger('user_id');
            $table->string('number');
            $table->string('bank');
            $table->string('name');
            $table->string('email');
            $table->string('customer_code');
            $table->string('currency');
            $table->string('status');
            $table->string('model')->default('user'); // user, investor
            $table->timestamps();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_accounts');
    }
};
