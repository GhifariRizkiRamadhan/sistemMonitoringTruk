<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('truck_drivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('truck_id');
            $table->unsignedBigInteger('driver_id');
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('truck_id')->references('id')->on('trucks');
            $table->foreign('driver_id')->references('id')->on('drivers');
        });
    }

    public function down()
    {
        Schema::dropIfExists('truck_drivers');
    }
};
