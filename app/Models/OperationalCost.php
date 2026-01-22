<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalCost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'category',
        'description',
        'amount',
        'cost_date',
        'status',
        'notes',
        'receipt_file',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cost_date' => 'date',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created the operational cost
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the operational cost
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: Get recorded costs
     */
    public function scopeRecorded($query)
    {
        return $query->where('status', 'recorded');
    }

    /**
     * Scope: Get approved costs
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Filter by period
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('cost_date', [$startDate, $endDate]);
    }

    /**
     * Approve the operational cost
     */
    public function approve(int $userId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Reject the operational cost
     */
    public function reject(): void
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }
}
