<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('monthly_checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('truck_id');
            $table->date('check_date');
            $table->text('description')->nullable();
            $table->integer('month');
            $table->integer('year');
            $table->enum('tire_condition', ['good', 'fair', 'needs_repair']);
            $table->integer('current_km');
            $table->integer('service_km_remaining');
            $table->enum('brake_condition', ['good', 'fair', 'needs_repair']);
            $table->enum('cabin_condition', ['good', 'fair', 'needs_repair']);
            $table->enum('cargo_area_condition', ['good', 'fair', 'needs_repair']);
            $table->enum('lights_condition', ['good', 'fair', 'needs_repair']);
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('truck_id')->references('id')->on('trucks');
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_checks');
    }
};
