<?php

use App\Enums\EntityType;
use App\Models\Currency;
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
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignIdFor(Currency::class)->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('logo_id')->nullable()->references('id')->on('media')->cascadeOnDelete();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('company_registration')->nullable();
            $table->string('company_kra_pin')->nullable();
            $table->enum('entity_type', EntityType::values())->nullable()->default(EntityType::DEFAULT);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
};
