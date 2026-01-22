<?php

namespace App\Traits;

trait Timestampable
{
    public function getCreatedAtAttribute()
    {
        return $this->attributes['created_at'] ?? null;
    }

    public function getUpdatedAtAttribute()
    {
        return $this->attributes['updated_at'] ?? null;
    }

    public function wasRecentlyCreated(): bool
    {
        return $this->created_at?->diffInMinutes() < 5;
    }

    public function wasRecentlyUpdated(): bool
    {
        return $this->updated_at?->diffInMinutes() < 5;
    }
}
