<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\Composite\Combination;
use PHPUnit\Framework\TestCase;

class CombinationTest extends TestCase
{
    public function testToArray()
    {
        $combination = new Combination('foo');

        $this->assertSame(['foo'], $combination->toArray());
    }

    public function testAdd()
    {
        $combination = new Combination('foo');
        $combination2 = $combination->add('baz');

        $this->assertInstanceOf(Combination::class, $combination2);
        $this->assertNotSame($combination, $combination2);
        $this->assertSame(['foo'], $combination->toArray());
        $this->assertSame(['baz', 'foo'], $combination2->toArray());
    }
}
