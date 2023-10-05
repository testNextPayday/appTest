<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSecondaryCollectionPlanToEmployers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
   
        Schema::table('employers', function (Blueprint $table) {
            
            $desc = '100 -> Remita (DDM), 200 -> Remita (DAS), 201 -> IPPIS (DAS)';
            $desc .= '300 -> Paystack (Card)';
        
            $table->integer('collection_plan_secondary')
                    ->after('collection_plan')
                    ->nullable()
                    ->comment($desc);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employers', function (Blueprint $table) {
            $table->dropColumn('collection_plan_secondary');
        });
    }
}
