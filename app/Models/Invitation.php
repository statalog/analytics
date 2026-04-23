<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invitation extends Model
{
    protected $fillable = ['owner_id', 'email', 'role', 'sites_json', 'token', 'accepted_at', 'expires_at'];

    protected $casts = [
        'accepted_at' => 'datetime',
        'expires_at'  => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast() && $this->accepted_at === null;
    }

    /** Site IDs restricted to (null = all sites). */
    public function siteIds(): ?array
    {
        return $this->sites_json ? json_decode($this->sites_json, true) : null;
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }
}
