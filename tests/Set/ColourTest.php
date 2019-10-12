<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Colour,
    Set,
};
use Innmind\Colour\RGBA;
use PHPUnit\Framework\TestCase;

class ColourTest extends TestCase
{
    public function testOf()
    {
        $colours = Colour::of();

        $this->assertInstanceOf(Set::class, $colours);
        $this->assertCount(100, \iterator_to_array($colours->values()));

        foreach ($colours->values() as $colour) {
            $this->assertInstanceOf(RGBA::class, $colour);
        }
    }
}
