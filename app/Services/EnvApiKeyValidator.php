<?php

namespace App\Services;

use App\Contracts\ApiKeyContext;
use App\Contracts\ApiKeyValidator;

class EnvApiKeyValidator implements ApiKeyValidator
{
    public function validate(string $key): ?ApiKeyContext
    {
        $configured = config('statalog.api_key', '');

        if ($configured === '' || !hash_equals($configured, $key)) {
            return null;
        }

        return new ApiKeyContext(userId: null); // OSS: no user scope
    }
}
