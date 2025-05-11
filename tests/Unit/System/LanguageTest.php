<?php

declare(strict_types=1);

namespace Tests\Unit\System;

use App\System\Language;
use Tests\TestCase;

class LanguageTest extends TestCase
{
    public function testTryFromAnyFromLower(): void
    {
        $this->assertEquals(Language::en, Language::tryFromAny('en'));
    }

    public function testTryFromAnyFromUpper(): void
    {
        $this->assertEquals(Language::en, Language::tryFromAny('EN'));
    }

    public function testTryFromAnyNull(): void
    {
        $this->assertNull(Language::tryFromAny(null));
    }

    public function testExceptionFromInvalid(): void
    {
        $this->expectException(\ValueError::class);

        Language::fromAny('invalid');
    }

    public function testFromAnyArray(): void
    {
        $result = Language::fromAnyArray(['en', 'pl']);

        $this->assertEquals(
            [
                Language::en,
                Language::pl,
            ],
            $result,
        );
    }

    public function testLower(): void
    {
        $this->assertEquals('en', Language::en->lower());
    }

    public function testUpper(): void
    {
        $this->assertEquals('EN', Language::en->upper());
    }

    public function testTitleLower(): void
    {
        $this->assertEquals('english', Language::en->titleLower());
    }
}
