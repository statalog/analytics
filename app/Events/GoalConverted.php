<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GoalConverted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly int    $userId,
        public readonly int    $siteId,
        public readonly string $siteUuid,
        public readonly int    $goalId,
        public readonly string $goalName,
        public readonly float  $monetaryValue,
        public readonly string $visitorId,
        public readonly string $sessionId,
        public readonly string $url,
    ) {}
}
