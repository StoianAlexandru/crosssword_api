<?php

use App\Enum\Model\CrosswordDirectionEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crosswords', function (Blueprint $table) {
            $table->id();
            $table->string('answer', 255);
            $table->string('clue', 255);
            $table->enum('direction', CrosswordDirectionEnum::values());
            $table->unsignedSmallInteger('length');
            $table->date('date')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crosswords');
    }
};
