<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\{
    Composite\Combination,
    Value,
};
use PHPUnit\Framework\TestCase;

class CombinationTest extends TestCase
{
    public function testToArray()
    {
        $combination = new Combination(Value::immutable('foo'));

        $this->assertSame(['foo'], $combination->unwrap());
    }

    public function testAdd()
    {
        $combination = new Combination(Value::immutable('foo'));
        $combination2 = $combination->add(Value::immutable('baz'));

        $this->assertInstanceOf(Combination::class, $combination2);
        $this->assertNotSame($combination, $combination2);
        $this->assertSame(['foo'], $combination->unwrap());
        $this->assertSame(['baz', 'foo'], $combination2->unwrap());
    }

    public function testIsImmutableIfAllValuesAreImmutable()
    {
        $immutable = new Combination(Value::immutable(42));
        $immutable = $immutable->add(Value::immutable(24));
        $mutable = $immutable->add(Value::mutable(fn() => new \stdClass));
        $immutable = $immutable->add(Value::immutable(66));

        $this->assertTrue($immutable->immutable());
        $this->assertFalse($mutable->immutable());
    }
}
