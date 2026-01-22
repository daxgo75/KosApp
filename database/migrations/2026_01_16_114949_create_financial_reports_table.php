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
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type', 50);
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_income', 12, 2);
            $table->decimal('total_operational_cost', 12, 2);
            $table->decimal('net_profit', 12, 2);
            $table->decimal('outstanding_payment', 12, 2);
            $table->integer('total_tenants');
            $table->integer('occupied_rooms');
            $table->integer('available_rooms');
            $table->text('summary')->nullable();
            $table->string('status', 32)->default('draft');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['report_type', 'period_start', 'period_end', 'status'], 'financial_reports_type_period_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_reports');
    }
};
