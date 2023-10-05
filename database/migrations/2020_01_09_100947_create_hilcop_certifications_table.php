<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHilcopCertificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hilcop_certifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference');
            $table->string('name');
            $table->string('email')->nullable();
            $table->integer('no_of_shares');
            $table->string('value_per_share');
            $table->timestamp('membership_date');
            $table->string('address');
            $table->string('certificate');
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
        Schema::dropIfExists('hilcop_certifications');
    }
}
