<?php

use App\Enums\AccountStatus;
use App\Enums\AccountType;
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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', AccountType::values())->default(AccountType::DEFAULT);
            $table->string('name', 100)->index();
            $table->string('number', 20);
            $table->foreignIdFor(Currency::class)->constrained()->cascadeOnDelete();
            $table->string('description')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', AccountStatus::values())->default(AccountStatus::DEFAULT);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_phone', 20)->nullable();
            $table->text('bank_address')->nullable();
            $table->string('bank_website', 255)->nullable();
            $table->string('bic_swift_code', 11)->nullable();
            $table->string('iban')->nullable();
            $table->string('aba_routing_number', 9)->nullable();
            $table->string('ach_routing_number', 9)->nullable();
            $table->boolean('enabled')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
