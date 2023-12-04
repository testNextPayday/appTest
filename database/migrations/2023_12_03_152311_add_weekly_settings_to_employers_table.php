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
        Schema::table('employers', function (Blueprint $table) {
            $table->double('weekly_rate_3')->default(0);
            $table->double('weekly_rate_6')->default(0);
            $table->double('weekly_rate_12')->default(0);
            $table->double('weekly_fees_3')->default(0);
            $table->double('weekly_fees_6')->default(0);
            $table->double('weekly_fees_12')->default(0);
            $table->integer('max_weekly_tenure')->default(12);
            $table->boolean('has_weekly_repayment')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employers', function (Blueprint $table) {
            //
        });
    }
};
