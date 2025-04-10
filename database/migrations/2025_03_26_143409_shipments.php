<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('truck_id');
            $table->unsignedBigInteger('cargo_type_id');
            $table->date('travel_money_date');
            $table->date('loading_date');
            $table->date('unloading_date');
            $table->decimal('travel_money', 15, 2);
            $table->decimal('tonnage', 10, 2);
            $table->decimal('wage_per_ton', 10, 2);
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('truck_id')->references('id')->on('trucks');
            $table->foreign('cargo_type_id')->references('id')->on('cargo_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('shipments');
    }
};
