<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'name',
        'division',
        'description',
        'location',
        'event_date',
        'price',
        'quota',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'datetime',
        ];
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}