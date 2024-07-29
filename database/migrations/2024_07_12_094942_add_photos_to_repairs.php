<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotosToRepairs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('repairs', function (Blueprint $table) {
            $table->text('before_photos')->nullable();
            $table->unsignedBigInteger('before_photos_added_by')->index()->nullable();
            $table->timestamp('before_photos_added_at')->nullable();
            $table->text('during_photos')->nullable();
            $table->unsignedBigInteger('during_photos_added_by')->index()->nullable();
            $table->timestamp('during_photos_added_at')->nullable();
            $table->text('after_photos')->nullable();
            $table->unsignedBigInteger('after_photos_added_by')->index()->nullable();
            $table->timestamp('after_photos_added_at')->nullable();
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
            $table->dropColumn('before_photos');
            $table->dropColumn('during_photos');
            $table->dropColumn('after_photos');
        });
    }
}
