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
        $combination = Combination::startWith(Value::of('foo'));

        $this->assertSame(['foo'], $combination->detonate(static fn(...$args) => $args));
    }

    public function testAdd()
    {
        $combination = Combination::startWith(Value::of('foo'));
        $combination2 = $combination->add(Value::of('baz'));

        $this->assertInstanceOf(Combination::class, $combination2);
        $this->assertNotSame($combination, $combination2);
        $this->assertSame(['foo'], $combination->detonate(static fn(...$args) => $args));
        $this->assertSame(['baz', 'foo'], $combination2->detonate(static fn(...$args) => $args));
    }

    public function testIsImmutableIfAllValuesAreImmutable()
    {
        $immutable = Combination::startWith(Value::of(42));
        $immutable = $immutable->add(Value::of(24));
        $mutable = $immutable->add(Value::of(new \stdClass)->flagMutable(true));
        $immutable = $immutable->add(Value::of(66));

        $this->assertTrue($immutable->immutable());
        $this->assertFalse($mutable->immutable());
    }

    public function testCombinationIsShrinkableAsLongAsAtLeastOneValueIsShrinkable()
    {
        $nonShrinkable = Combination::startWith(Value::of(42));
        $nonShrinkable = $nonShrinkable->add(Value::of(24));
        $nonShrinkable = $nonShrinkable->add(Value::of(66));

        $this->assertNull($nonShrinkable->aShrinkNth(0));
        $this->assertNull($nonShrinkable->aShrinkNth(1));
        $this->assertNull($nonShrinkable->aShrinkNth(2));

        $shrinkable = Combination::startWith(Value::of(42));
        $shrinkable = $shrinkable->add(
            Value::of(24)->shrinkWith(
                static fn() => Dichotomy::of(
                    Value::of(12),
                    Value::of(23),
                ),
            ),
        );
        $shrinkable = $shrinkable->add(Value::of(66));

        $this->assertNotNull($shrinkable->aShrinkNth(1));
    }
}
