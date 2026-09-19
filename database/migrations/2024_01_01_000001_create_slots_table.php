<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->string('board_id', 40);
            $table->unsignedInteger('slot_index');
            $table->string('label', 255)->default('');
            $table->string('url', 2048);

            $table->primary(['board_id', 'slot_index']);
            $table->foreign('board_id')->references('id')->on('boards')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
