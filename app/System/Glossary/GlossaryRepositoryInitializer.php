<?php

declare(strict_types=1);

namespace App\System\Glossary;

use Tempest\Container\Container;
use Tempest\Container\Initializer;

class GlossaryRepositoryInitializer implements Initializer
{
    public function initialize(Container $container): GlossaryRepository
    {
        return new FileGlossaryRepository();
    }
}
