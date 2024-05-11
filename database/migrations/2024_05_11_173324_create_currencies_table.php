<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbr');
            $table->string('code');
            $table->string('locale');
            $table->integer('precision');
            $table->string('subunit_name');
            $table->integer('subunit');
            $table->string('symbol');
            $table->boolean('symbol_first');
            $table->string('decimal_mark');
            $table->string('thousands_separator');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
