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
        Schema::create('create_child_models', function (Blueprint $table) {
            $table->id();
            $table->string('IsActive')->default('Y');
            $table->string('ChildName'); // Corrected column name
            $table->text('Gender');
            $table->string('SchoolName'); // Corrected column name
            $table->string('BusRoute'); // Corrected column name
            $table->string('ParentName'); // Corrected column name
            $table->string('ContactNo');
            $table->string('Address');
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
        Schema::dropIfExists('create_child_models');
    }
};
