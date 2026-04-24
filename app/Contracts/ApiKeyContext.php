<?php

namespace App\Contracts;

/**
 * Carries identity resolved from a validated API key.
 *
 * OSS: userId = null (single-tenant, full access).
 * Cloud: userId = key owner, siteId = null (all sites) or a specific site UUID.
 */
class ApiKeyContext
{
    public function __construct(
        public readonly ?int    $userId,
        public readonly ?string $siteId = null,
    ) {}

    public function isOss(): bool
    {
        return $this->userId === null;
    }
}
