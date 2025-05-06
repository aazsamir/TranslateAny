<?php

declare(strict_types=1);

namespace App\Auth;

use Tempest\Http\RequestHeaders;

readonly class Credentials
{
    public function __construct(
        public ?string $token,
    ) {
    }

    public static function fromBearer(RequestHeaders $headers): ?Credentials
    {
        return self::fromAuthToken($headers, 'Bearer');
    }

    public static function fromAuthToken(RequestHeaders $headers, string $tokenName): ?Credentials
    {
        $authorization = $headers->get('Authorization');

        if ($authorization === null) {
            return null;
        }

        if (! \str_starts_with($authorization, $tokenName)) {
            return null;
        }

        $authorization = substr($authorization, strlen($tokenName) + 1);

        if ($authorization === '') {
            return null;
        }

        return new Credentials($authorization);
    }
}
