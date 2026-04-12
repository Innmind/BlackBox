<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    PHPUnit\BlackBox,
    PHPUnit\Framework\TestCase,
};

class UnicodeTest extends TestCase
{
    use BlackBox;

    public function testBlocks(): BlackBox\Proof
    {
        return $this
            ->forAll(Set::strings()->unicode()->char())
            ->prove(function($string) {
                $this->assertIsString($string);
            });
    }
}
