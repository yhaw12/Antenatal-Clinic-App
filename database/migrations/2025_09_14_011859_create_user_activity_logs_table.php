<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);                // e.g. "login", "logout", "create_patient"
            $table->string('model_type')->nullable();     // optional: App\Models\Patient
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('details')->nullable();          // human readable details or JSON
            $table->json('meta')->nullable();             // structured metadata (ip, user_agent, route, payload)
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id']);
            $table->index(['action']);
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_activity_logs');
    }
};
