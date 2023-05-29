<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Type,
    Set,
    Random,
};

class TypeTest extends TestCase
{
    public function testAny()
    {
        $types = Type::any();

        $this->assertInstanceOf(Set::class, $types);
        $this->assertCount(100, \iterator_to_array($types->values(Random::mersenneTwister)));
    }
}
