<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the user that preference
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}