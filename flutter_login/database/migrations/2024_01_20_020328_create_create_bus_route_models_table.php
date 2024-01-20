<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('create_bus_route_models', function (Blueprint $table) {
            $table->id();
            $table->string('N_BusRoute_From');  // Consider using ->text() if longer text might be needed
            $table->string('N_BusRoute_To');    // Consider using ->text() if longer text might be needed
            $table->unsignedDecimal('N_BusRouteFare_Amount', 8, 2);  // Adjust precision and scale based on your requirements
            $table->decimal('N_BusRoute_Latitude', 10, 7);  // Adjust precision and scale based on your requirements
            $table->decimal('N_BusRoute_Longitude', 10, 7);
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
        Schema::dropIfExists('create_bus_route_models');
    }
};
