<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('operational_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('truck_id');
            $table->unsignedBigInteger('operational_type_id');
            $table->text('description')->nullable();
            $table->date('date');
            $table->integer('quantity');
            $table->decimal('price', 15, 2);
            $table->timestamp('created_at')->useCurrent();
            
            $table->foreign('truck_id')->references('id')->on('trucks');
            $table->foreign('operational_type_id')->references('id')->on('operational_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('operational_expenses');
    }
};
