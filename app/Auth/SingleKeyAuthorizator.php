<?php

declare(strict_types=1);

namespace App\Auth;

class SingleKeyAuthorizator implements Authorizator
{
    public function __construct(
        private string $token,
    ) {
    }

    public function isAuthenticated(?Credentials $credentials): bool
    {
        if ($credentials === null) {
            return false;
        }

        return $credentials->token === $this->token;
    }
}
