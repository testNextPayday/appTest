<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Doctrine\DBAL\Types\FloatType;
use Doctrine\DBAL\Types\Type;

class AddRatesToEmployersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Type::hasType('double')) {
            Type::addType('double', FloatType::class);
        }
        Schema::table('employers', function (Blueprint $table) {            
            $table->double('loan_vat_fee')->default(0);
            $table->double('interest_vat_fee')->default(0);
            $table->double('vat_fee')->default(0)->change();
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
            $table->dropColumn(['loan_vat_fee']);
            $table->dropColumn(['interest_vat_fee']);
            $table->dropColumn(['vat_fee']);
        });
    }
}
