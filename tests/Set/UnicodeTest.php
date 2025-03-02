<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Unicode,
    PHPUnit\BlackBox,
    PHPUnit\Framework\TestCase,
};

class UnicodeTest extends TestCase
{
    use BlackBox;

    public function testStrings()
    {
        $this
            ->forAll(Unicode::strings())
            ->then(function($string) {
                $this->assertIsString($string);
            });
    }

    public function testChar()
    {
        $this
            ->forAll(Unicode::any())
            ->then(function($string) {
                $this->assertIsString($string);
            });
    }
}
