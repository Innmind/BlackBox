<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Elements,
    Set,
    Set\Value,
    Random\MtRand,
    Exception\EmptySet,
};

class ElementsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, Elements::of(42));
    }

    public function testOf()
    {
        $this->assertInstanceOf(Elements::class, Elements::of(42, 24));
    }

    public function testTake100ValuesByDefault()
    {
        $elements = Elements::of(...\range(0, 1000));
        $values = $this->unwrap($elements->values(new MtRand));

        $this->assertCount(100, $values);
    }

    public function testTake()
    {
        $elements = Elements::of(...\range(0, 1000));
        $elements2 = $elements->take(10);
        $aValues = $this->unwrap($elements->values(new MtRand));
        $bValues = $this->unwrap($elements2->values(new MtRand));

        $this->assertInstanceOf(Elements::class, $elements2);
        $this->assertNotSame($elements, $elements2);
        $this->assertCount(100, $aValues);
        $this->assertCount(10, $bValues);
    }

    public function testFilter()
    {
        $elements = Elements::of(...\range(0, 1000));
        $elements2 = $elements->filter(static function(int $value): bool {
            return $value % 2 === 0;
        });
        $containsEvenInt = static function(bool $containsEvenInt, int $value): bool {
            return $containsEvenInt || ($value % 2 === 1);
        };

        $this->assertInstanceOf(Elements::class, $elements2);
        $this->assertNotSame($elements, $elements2);
        $this->assertFalse(
            \array_reduce(
                $this->unwrap($elements2->values(new MtRand)),
                $containsEvenInt,
                false,
            ),
        );
        $this->assertTrue(
            \array_reduce(
                $this->unwrap($elements->values(new MtRand)),
                $containsEvenInt,
                false,
            ),
        );
    }

    public function testValues()
    {
        $elements = Elements::of(...\range(0, 1000));

        $this->assertInstanceOf(\Generator::class, $elements->values(new MtRand));
        $this->assertCount(100, $this->unwrap($elements->values(new MtRand)));

        foreach ($elements->values(new MtRand) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testElementsAreNotShrinkable()
    {
        $elements = Elements::of(...\range(0, 1000));

        foreach ($elements->values(new MtRand) as $value) {
            $this->assertFalse($value->shrinkable());
        }
    }

    public function testThereIsAlwaysTheSpecifiedNumberOfElementsReturnedEvenThoughLessInjected()
    {
        $elements = Elements::of('foo', 'bar', 'baz');
        $values = \iterator_to_array($elements->values(new MtRand));

        $this->assertCount(100, $values);

        $values = \array_map(static fn($v) => $v->unwrap(), $values);
        $frequency = \array_count_values($values);

        $this->assertGreaterThan(1, $frequency['foo']);
        $this->assertGreaterThan(1, $frequency['bar']);
        $this->assertGreaterThan(1, $frequency['baz']);
    }

    public function testTakeNoElement()
    {
        $this->assertCount(
            0,
            \iterator_to_array(
                Elements::of(1, 2, 3)
                    ->take(0)
                    ->values(new MtRand),
            ),
        );
    }

    public function testThrowWhenCannotFindAValue()
    {
        $this->expectException(EmptySet::class);

        Elements::of(1)
            ->filter(static fn() => false)
            ->values(new MtRand)
            ->current();
    }
}
