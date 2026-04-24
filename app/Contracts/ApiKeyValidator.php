<?php

namespace App\Contracts;

interface ApiKeyValidator
{
    /**
     * Validate an API key string.
     *
     * Returns an ApiKeyContext on success, null on failure.
     */
    public function validate(string $key): ?ApiKeyContext;
}
