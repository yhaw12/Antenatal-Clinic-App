<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exported_by')->constrained('users');
            $table->string('file_path');
            $table->json('filters')->nullable();
            $table->boolean('encrypted')->default(true);
            $table->timestamps();
        });
    }
    public function down() {
        Schema::dropIfExists('exports');
    }
};
