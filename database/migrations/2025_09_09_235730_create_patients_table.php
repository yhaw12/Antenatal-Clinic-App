<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            // encrypted fields stored as normal strings
            $table->string('first_name')->nullable(false);
            $table->string('last_name')->nullable();
            $table->string('folder_no')->nullable();
            $table->string('phone')->nullable();
            $table->string('name_search')->nullable()->index();
            $table->string('phone_search')->nullable()->index();
            $table->string('whatsapp')->nullable();
            $table->string('room')->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->string('id_number')->nullable();
            $table->string('hospital_number')->nullable();
            $table->date('next_review_date')->nullable();
            $table->text('address')->nullable();
            $table->text('complaints')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('patients');
    }
};
