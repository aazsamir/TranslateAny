<?php

declare(strict_types=1);

namespace App\Auth;

class NoopAuthorizator implements Authorizator
{
    public function isAuthenticated(?Credentials $credentials): bool
    {
        return true;
    }
}
