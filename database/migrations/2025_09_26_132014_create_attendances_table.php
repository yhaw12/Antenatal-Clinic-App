<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_present')->default(false);
            $table->timestamps();

            $table->unique(['patient_id','date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
