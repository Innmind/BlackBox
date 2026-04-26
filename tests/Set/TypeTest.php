<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

class TypeTest extends TestCase
{
    public function testAny()
    {
        $types = Set::type()->take(100);

        $this->assertCount(100, \iterator_to_array($types->values(Random::mersenneTwister)));
    }
}
