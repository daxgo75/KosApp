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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method');
            $table->string('status')->default('pending');
            $table->date('payment_date');
            $table->date('due_date');
            $table->date('month_year');
            $table->text('notes')->nullable();
            $table->string('reference_code')->unique()->nullable();
            $table->string('proof_file')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['tenant_id', 'room_id', 'status', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
