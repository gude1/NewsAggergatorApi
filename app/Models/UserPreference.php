<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     title="UserPreference",
 *     description="UserPreference model",
 *     @OA\Xml(
 *         name="UserPreference"
 *     )
 * )
 */
class UserPreference extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the user that preference
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @OA\Relation(
     *     name="user",
     *     type="object",
     *     schema=@OA\Schema(ref="#/components/schemas/User")
     * )
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}