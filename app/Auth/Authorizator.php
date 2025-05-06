<?php

declare(strict_types=1);

namespace App\Auth;

interface Authorizator
{
    public function isAuthenticated(?Credentials $credentials): bool;
}
