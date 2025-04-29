<?php

declare(strict_types=1);

namespace App\Front;

use Tempest\Router\Get;
use Tempest\View\View;

use function Tempest\view;

class FrontController
{
    #[Get('/')]
    public function __invoke(): View
    {
        return view('./front.view.php');
    }
}
