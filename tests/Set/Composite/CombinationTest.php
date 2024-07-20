<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\{
    Set\Composite\Combination,
    Set\Value,
    Set\Dichotomy,
    PHPUnit\Framework\TestCase,
};

class CombinationTest extends TestCase
{
    public function testToArray()
    {
        $combination = Combination::startWith(Value::immutable('foo'));

        $this->assertSame(['foo'], $combination->unwrap());
    }

    public function testAdd()
    {
        $combination = Combination::startWith(Value::immutable('foo'));
        $combination2 = $combination->add(Value::immutable('baz'));

        $this->assertInstanceOf(Combination::class, $combination2);
        $this->assertNotSame($combination, $combination2);
        $this->assertSame(['foo'], $combination->unwrap());
        $this->assertSame(['baz', 'foo'], $combination2->unwrap());
    }

    public function testIsImmutableIfAllValuesAreImmutable()
    {
        $immutable = Combination::startWith(Value::immutable(42));
        $immutable = $immutable->add(Value::immutable(24));
        $mutable = $immutable->add(Value::mutable(static fn() => new \stdClass));
        $immutable = $immutable->add(Value::immutable(66));

        $this->assertTrue($immutable->immutable());
        $this->assertFalse($mutable->immutable());
    }

    public function testCombinationIsShrinkableAsLongAsAtLeastOneValueIsShrinkable()
    {
        $nonShrinkable = Combination::startWith(Value::immutable(42));
        $nonShrinkable = $nonShrinkable->add(Value::immutable(24));
        $nonShrinkable = $nonShrinkable->add(Value::immutable(66));

        $this->assertFalse($nonShrinkable->shrinkable());

        $shrinkable = Combination::startWith(Value::immutable(42));
        $shrinkable = $shrinkable->add(Value::immutable(
            24,
            new Dichotomy(
                static fn() => Value::immutable(12),
                static fn() => Value::immutable(23),
            ),
        ));
        $shrinkable = $shrinkable->add(Value::immutable(66));

        $this->assertTrue($shrinkable->shrinkable());
    }
}
