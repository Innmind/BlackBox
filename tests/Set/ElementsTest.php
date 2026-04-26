<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Value,
    Random,
    Exception\EmptySet,
};

class ElementsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, Set::of(42));
    }

    public function testTake100ValuesByDefault()
    {
        $elements = Set::of(...\range(0, 1000));
        $values = $this->unwrap($elements->take(100)->values(Random::mersenneTwister));

        $this->assertCount(100, $values);
    }

    public function testTake()
    {
        $elements = Set::of(...\range(0, 1000));
        $elements2 = $elements->take(10);
        $aValues = $this->unwrap($elements->take(100)->values(Random::mersenneTwister));
        $bValues = $this->unwrap($elements2->values(Random::mersenneTwister));

        $this->assertInstanceOf(Set::class, $elements2);
        $this->assertNotSame($elements, $elements2);
        $this->assertCount(100, $aValues);
        $this->assertCount(10, $bValues);
    }

    public function testFilter()
    {
        $elements = Set::of(...\range(0, 1000))->take(100);
        $elements2 = $elements->filter(static function(int $value): bool {
            return $value % 2 === 0;
        });
        $containsEvenInt = static function(bool $containsEvenInt, int $value): bool {
            return $containsEvenInt || ($value % 2 === 1);
        };

        $this->assertInstanceOf(Set::class, $elements2);
        $this->assertNotSame($elements, $elements2);
        $this->assertFalse(
            \array_reduce(
                $this->unwrap($elements2->values(Random::mersenneTwister)),
                $containsEvenInt,
                false,
            ),
        );
        $this->assertTrue(
            \array_reduce(
                $this->unwrap($elements->values(Random::mersenneTwister)),
                $containsEvenInt,
                false,
            ),
        );
    }

    public function testValues()
    {
        $elements = Set::of(...\range(0, 1000))->take(100);

        $this->assertInstanceOf(\Generator::class, $elements->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($elements->values(Random::mersenneTwister)));

        foreach ($elements->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
        }
    }

    public function testElementsAreNotShrinkable()
    {
        $elements = Set::of(...\range(0, 1000))->take(100);

        foreach ($elements->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }
    }

    public function testThereIsAlwaysTheSpecifiedNumberOfElementsReturnedEvenThoughLessInjected()
    {
        $elements = Set::of('foo', 'bar', 'baz')->take(100);
        $values = \iterator_to_array($elements->values(Random::mersenneTwister));

        $this->assertCount(100, $values);

        $values = \array_map(static fn($v) => $v->unwrap(), $values);
        $frequency = \array_count_values($values);

        $this->assertGreaterThan(1, $frequency['foo']);
        $this->assertGreaterThan(1, $frequency['bar']);
        $this->assertGreaterThan(1, $frequency['baz']);
    }

    public function testThrowWhenCannotFindAValue()
    {
        $this->assert()->throws(
            static fn() => Set::of(1)
                ->filter(static fn() => false)
                ->values(Random::mersenneTwister)
                ->current(),
            EmptySet::class,
        );
    }
}
