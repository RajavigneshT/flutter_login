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
            $table->string('N_BusRoute_From');
            $table->string('N_BusRoute_To');
            $table->unsignedDecimal('N_BusRouteFare_Amount', 8, 2);
            $table->decimal('N_BusRoute_Latitude', 10, 7)->default(null);
            $table->decimal('N_BusRoute_Longitude', 10, 7)->default(null);
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
