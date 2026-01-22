<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TenantPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'photo_type',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'status',
        'notes',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tenant that owns the photo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the full URL of the photo
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Scope: Get active photos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Filter by photo type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('photo_type', $type);
    }

    /**
     * Delete photo file from storage when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($photo) {
            if (Storage::exists($photo->file_path)) {
                Storage::delete($photo->file_path);
            }
        });
    }
}
