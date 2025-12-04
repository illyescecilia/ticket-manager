<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'barcode',
        'admission_time',
        'user_id',
        'event_id',
        'seat_id',
        'price',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'admission_time' => 'datetime',
        ];
    }

    /**
     * Get the ticket's user.
     */
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ticket's event.
     */
    public function event(): BelongsTo {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the ticket's seat.
     */
    public function seat(): BelongsTo {
        return $this->belongsTo(Seat::class);
    }
}
