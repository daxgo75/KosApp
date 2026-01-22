<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialReport extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_type',
        'period_start',
        'period_end',
        'total_income',
        'total_operational_cost',
        'net_profit',
        'outstanding_payment',
        'total_tenants',
        'occupied_rooms',
        'available_rooms',
        'summary',
        'status',
        'created_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_income' => 'decimal:2',
        'total_operational_cost' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'outstanding_payment' => 'decimal:2',
        'total_tenants' => 'integer',
        'occupied_rooms' => 'integer',
        'available_rooms' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created the report
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Get published reports
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope: Get draft reports
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope: Filter by report type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    /**
     * Scope: Filter by period
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->where('period_start', '>=', $startDate)
            ->where('period_end', '<=', $endDate);
    }

    /**
     * Publish the report
     */
    public function publish(): void
    {
        $this->update(['status' => 'published']);
    }

    /**
     * Archive the report
     */
    public function archive(): void
    {
        $this->update(['status' => 'archived']);
    }

    /**
     * Calculate profit margin percentage
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->total_income == 0) {
            return 0;
        }
        return ($this->net_profit / $this->total_income) * 100;
    }
}
