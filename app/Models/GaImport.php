<?php
/**
 * Statalog - Open source web analytics.
 * https://statalog.com
 * @license AGPL-3.0
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GaImport extends Model
{
    protected $table = 'ga_imports';

    protected $fillable = [
        'user_id', 'site_id', 'ga_property_id', 'ga_property_name',
        'from_date', 'to_date', 'status',
        'total_days', 'processed_days', 'error_message',
        'started_at', 'completed_at',
    ];

    protected $casts = [
        'from_date'    => 'date',
        'to_date'      => 'date',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function progressPercent(): int
    {
        if ($this->total_days === 0) return 0;
        return (int) min(100, round(($this->processed_days / $this->total_days) * 100));
    }

    public function isDone(): bool
    {
        return in_array($this->status, ['completed', 'failed'], true);
    }
}
