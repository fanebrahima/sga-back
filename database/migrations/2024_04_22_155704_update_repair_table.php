<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRepairTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->unsignedBigInteger('shock_point_id')->index()->nullable();
            $table->unsignedBigInteger('brand_id')->index()->nullable();
            $table->text('emails')->nullable();
            $table->text('expert_signature')->nullable();
            $table->text('repairer_signature')->nullable();
            $table->text('customer_signature')->nullable();

            $table->foreign('brand_id')
                ->references('id')
                ->on('brands')
                ->onDelete('cascade');

            $table->foreign('shock_point_id')
                ->references('id')
                ->on('shock_points')
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
            $table->dropConstrainedForeignId('brand_id');
            $table->dropConstrainedForeignId('shock_point_id');
            $table->dropColumn('emails');
            $table->dropColumn('expert_signature');
            $table->dropColumn('repairer_signature');
            $table->dropColumn('customer_signature');
        });
    }
}
