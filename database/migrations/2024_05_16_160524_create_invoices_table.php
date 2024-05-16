<?php

use App\Models\Company;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Quote;
use App\Models\Task;
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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Task::class)->nullable()->cascadeOnDelete();
            $table->foreignIdFor(Customer::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Currency::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Company::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Quote::class)->nullable()->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('subtotal');
            $table->unsignedInteger('taxes');
            $table->unsignedBigInteger('total');
            $table->integer('serial_number')->nullable();
            $table->string('serial')->nullable();
            $table->json('items');
            $table->longText('notes');
            $table->boolean('mail');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
