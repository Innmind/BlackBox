<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\{
    Composite\Combination,
    Value,
    Dichotomy,
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

    public function testCombinationIsShrinkableAsLongAsAtLeastOneValueIsShrinkable()
    {
        $nonShrinkable = new Combination(Value::immutable(42));
        $nonShrinkable = $nonShrinkable->add(Value::immutable(24));
        $nonShrinkable = $nonShrinkable->add(Value::immutable(66));

        $this->assertFalse($nonShrinkable->shrinkable());

        $shrinkable = new Combination(Value::immutable(42));
        $shrinkable = $shrinkable->add(Value::immutable(
            24,
            new Dichotomy(
                fn() => Value::immutable(12),
                fn() => Value::immutable(23),
            ),
        ));
        $shrinkable = $shrinkable->add(Value::immutable(66));

        $this->assertTrue($shrinkable->shrinkable());
    }

    public function testShrinkUsesTheFirstValueThatIsShrinkableToBuildItsOwnDichotomy()
    {
        $combination = new Combination(Value::immutable(
            66,
            new Dichotomy(
                fn() => Value::immutable(33),
                fn() => Value::immutable(65),
            ),
        ));
        $combination = $combination->add(Value::immutable(
            24,
            new Dichotomy(
                fn() => Value::immutable(12),
                fn() => Value::immutable(23),
            ),
        ));
        $combination = $combination->add(Value::immutable(42));

        $shrinked = $combination->shrink();

        $this->assertIsArray($shrinked);
        $this->assertCount(2, $shrinked);
        $this->assertInstanceOf(Combination::class, $shrinked['a']);
        $this->assertInstanceOf(Combination::class, $shrinked['b']);
        $this->assertSame(
            [42, 12, 66],
            $shrinked['a']->unwrap(),
        );
        $this->assertSame(
            [42, 23, 66],
            $shrinked['b']->unwrap(),
        );
        $this->assertSame(
            [42, 12, 33],
            $shrinked['a']->shrink()['a']->unwrap(),
        );
        $this->assertSame(
            [42, 12, 65],
            $shrinked['a']->shrink()['b']->unwrap(),
        );
        $this->assertSame(
            [42, 23, 65],
            $shrinked['b']->shrink()['b']->unwrap(),
        );
        $this->assertSame(
            [42, 23, 33],
            $shrinked['b']->shrink()['a']->unwrap(),
        );
    }
}
