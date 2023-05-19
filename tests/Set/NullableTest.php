<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Nullable,
    Set\Integers,
    Random\MtRand,
};

class NullableTest extends TestCase
{
    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Nullable::of(Integers::any())->values(new MtRand));

        $this->assertContains(null, $values);
    }
}
