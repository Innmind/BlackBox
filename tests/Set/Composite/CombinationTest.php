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

        $this->assertSame(['foo'], $combination->detonate(static fn(...$args) => $args));
    }

    public function testAdd()
    {
        $combination = Combination::startWith(Value::immutable('foo'));
        $combination2 = $combination->add(Value::immutable('baz'));

        $this->assertInstanceOf(Combination::class, $combination2);
        $this->assertNotSame($combination, $combination2);
        $this->assertSame(['foo'], $combination->detonate(static fn(...$args) => $args));
        $this->assertSame(['baz', 'foo'], $combination2->detonate(static fn(...$args) => $args));
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

        $this->assertNull($nonShrinkable->aShrinkNth(0));
        $this->assertNull($nonShrinkable->aShrinkNth(1));
        $this->assertNull($nonShrinkable->aShrinkNth(2));

        $shrinkable = Combination::startWith(Value::immutable(42));
        $shrinkable = $shrinkable->add(
            Value::immutable(24)->shrinkWith(
                new Dichotomy(
                    static fn() => Value::immutable(12),
                    static fn() => Value::immutable(23),
                ),
            ),
        );
        $shrinkable = $shrinkable->add(Value::immutable(66));

        $this->assertNotNull($shrinkable->aShrinkNth(1));
    }
}
