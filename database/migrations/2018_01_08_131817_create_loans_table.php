 <?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference');
            $table->integer('request_id');
            $table->integer('user_id');
            $table->double('amount');
            $table->double('interest_percentage');
            $table->datetime('due_date');
            $table->double('emi')->nullable();
            $table->enum('status', [0, 1, 2, 3, 4])->default(0)->comment('0 - Processing, 1 - Active, 2 - Fulfilled, 3 - Defaulting, 4 - Cancelled');
            $table->string('collector_type')->default('App\\Models\\User')->comment('1 - User, 2 - Admin, 3 - Staff');
            $table->integer('collector_id')->nullable()->comment('For staff and admin');
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
        Schema::dropIfExists('loans');
    }
}
