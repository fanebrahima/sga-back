<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShockIdToRepairWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repair_works', function (Blueprint $table) {
            $table->unsignedBigInteger('shock_id')->index()->nullable();

            $table->foreign('shock_id')
                ->references('id')
                ->on('shocks')
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
        Schema::table('repair_works', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shock_id');
        });
    }
}
