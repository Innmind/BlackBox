<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Elements,
    Set,
    Set\Value,
};

class ElementsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, new Elements(42));
    }

    public function testOf()
    {
        $this->assertInstanceOf(Elements::class, Elements::of(42, 24));
    }

    public function testTake100ValuesByDefault()
    {
        $elements = Elements::of(...range(0, 1000));
        $values = $this->unwrap($elements->values());

        $this->assertCount(100, $values);
    }

    public function testTake()
    {
        $elements = Elements::of(...range(0, 1000));
        $elements2 = $elements->take(10);
        $aValues = $this->unwrap($elements->values());
        $bValues = $this->unwrap($elements2->values());

        $this->assertInstanceOf(Elements::class, $elements2);
        $this->assertNotSame($elements, $elements2);
        $this->assertCount(100, $aValues);
        $this->assertCount(10, $bValues);
    }

    public function testFilter()
    {
        $elements = Elements::of(...range(0, 1000));
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
                $this->unwrap($elements2->values()),
                $containsEvenInt,
                false,
            ),
        );
        $this->assertTrue(
            \array_reduce(
                $this->unwrap($elements->values()),
                $containsEvenInt,
                false,
            ),
        );
    }

    public function testValues()
    {
        $elements = Elements::of(...range(0, 1000));

        $this->assertInstanceOf(\Generator::class, $elements->values());
        $this->assertCount(100, $this->unwrap($elements->values()));

        foreach ($elements->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }
}
