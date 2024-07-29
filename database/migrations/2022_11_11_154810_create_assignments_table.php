<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->uuid('uuid')->unique();
            $table->string('number')->nullable();
            $table->string('client_first_name')->nullable();
            $table->string('client_last_name')->nullable();
            $table->string('client_phone')->nullable();
            $table->string('policy_number')->nullable();
            $table->string('disaster_number')->nullable();
            $table->dateTime('disaster_date')->nullable();
            $table->dateTime('expertise_date')->nullable();
            $table->string('expertise_address')->nullable();

            $table->string('car_immatriculation')->nullable();
            $table->string('car_brand')->nullable();
            $table->string('car_model')->nullable();
            $table->string('car_color')->nullable();
            $table->string('car_nb_place')->nullable();
            $table->string('car_fiscal_powerful')->nullable();
            $table->string('car_energy')->nullable();
            $table->string('car_serial_number')->nullable();
            $table->string('car_gender')->nullable();
            $table->string('car_km_comptor')->nullable();
            $table->string('car_general_state')->nullable();
            $table->string('car_new_value')->nullable();
            $table->string('car_depreciation')->nullable();
            $table->string('car_market_value')->nullable();
            $table->string('car_first_circulation_date')->nullable();

            $table->integer('etat')->nullable();
            $table->unsignedBigInteger('status_id')->index()->nullable();
            $table->unsignedBigInteger('created_by')->index()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->unsignedBigInteger('updated_by')->index()->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->index()->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('status_id')
                ->references('id')
                ->on('statuses')
                ->onDelete('cascade');

            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('assignments');
    }
}
