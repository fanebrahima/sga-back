<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisasterNumberToRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->unsignedBigInteger('insurer_id')->index()->nullable();
            $table->string('disaster_number')->nullable();

            $table->foreign('insurer_id')
                ->references('id')
                ->on('insurers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('insurer_id');
            $table->dropColumn('disaster_number');
        });
    }
}
