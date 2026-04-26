<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

class NullableTest extends TestCase
{
    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Set::integers()->nullable()->take(100)->values(Random::mersenneTwister));

        $this->assertContains(null, $values);
    }
}
