<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRepairWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repair_works', function (Blueprint $table) {
            $table->unsignedBigInteger('repair_id')->index()->nullable();

            $table->foreign('repair_id')
                ->references('id')
                ->on('repairs')
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
            $table->dropColumn('repair_id');
        });
    }
}
