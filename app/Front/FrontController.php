<?php

declare(strict_types=1);

namespace App\Front;

use App\Middleware\LogMiddleware;
use Tempest\Router\Get;
use Tempest\View\View;

use function Tempest\view;

class FrontController
{
    #[Get(
        uri: '/',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function index(): View
    {
        return view('./front.view.php');
    }

    #[Get(
        uri: '/documents',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function documents(): View
    {
        return view('./documents.view.php');
    }

    #[Get(
        uri: '/glossaries',
        middleware: [
            LogMiddleware::class,
        ],
    )]
    public function glossaries(): View
    {
        return view('./glossaries.view.php');
    }
}
