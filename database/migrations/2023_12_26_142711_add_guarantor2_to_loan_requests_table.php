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
        Schema::table('loan_requests', function (Blueprint $table) {
            $table->string('guarantor_first_name_2')->nullable();
            $table->string('guarantor_last_name_2')->nullable();
            $table->string('guarantor_phone_2')->nullable();
            $table->string('guarantor_email_2')->nullable();
            $table->string('guarantor_bvn_2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_requests', function (Blueprint $table) {
            //
        });
    }
};
