<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ward', function (Blueprint $table) {
            // This command creates an auto-incrementing big integer primary key named ward_id
            $table->bigIncrements('ward_id');

            $table->string('ward_name');
            $table->string('ward_type');
            $table->string('location');
            $table->integer('total_beds');
            $table->integer('available_beds');
            $table->integer('dept_id')->nullable();
            $table->string('ward_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ward');
    }
};