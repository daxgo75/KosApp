<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'identity_number',
        'identity_type',
        'identity_expiry_date',
        'address',
        'city',
        'province',
        'postal_code',
        'birth_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'identity_expiry_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $with = ['room'];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(TenantPhoto::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function activePhotos(): HasMany
    {
        return $this->photos()->where('status', 'active');
    }

    public function ktpPhotos(): HasMany
    {
        return $this->photos()->where('photo_type', 'ktp');
    }

    public function scopeByIdentityNumber($query, $identityNumber)
    {
        return $query->where('identity_number', $identityNumber);
    }
}
