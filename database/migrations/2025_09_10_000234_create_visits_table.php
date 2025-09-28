<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('user_id')->constrained('users'); // provider/nurse
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('seen_at')->nullable();
            $table->text('complaints')->nullable();
            $table->string('bp')->nullable();
            $table->decimal('weight',5,2)->nullable();
            $table->string('referral_to')->nullable();
            $table->text('chns_feedback')->nullable();
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('visits');
    }
};
