<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TeamMember extends Model
{
    protected $fillable = ['owner_id', 'user_id', 'role'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Sites the viewer is allowed to see. Empty = all owner's sites (admins always see all). */
    public function siteAccess(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'team_member_site_access')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    public function canAccessSite(Site $site): bool
    {
        if ($this->isAdmin()) return true;

        // No site-access rows means "all sites of the owner"
        $anyRestriction = $this->siteAccess()->exists();
        if (!$anyRestriction) return true;

        return $this->siteAccess()->where('sites.id', $site->id)->exists();
    }
}
