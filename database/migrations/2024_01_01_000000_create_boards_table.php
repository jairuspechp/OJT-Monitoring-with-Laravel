<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boards', function (Blueprint $table) {
            $table->string('id', 40)->primary();
            $table->string('name', 255);
            $table->string('layout_mode', 20)->default('standard');
            $table->unsignedInteger('slot_count')->default(4);
            $table->unsignedBigInteger('created_at'); // JS Date.now() in ms
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boards');
    }
};
