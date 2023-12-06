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
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name');
            $table->double('amount')->default(0);
            $table->double('target_amount')->default(0);
            $table->integer('interest')->default(0);
            $table->integer('duration')->default(0);
            $table->integer('target_duration')->default(0);
            $table->integer('credited_times')->default(0);
            $table->integer('status')->default(1);
            $table->timestamp('terminated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};
