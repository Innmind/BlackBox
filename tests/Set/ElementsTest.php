<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Elements,
    Set,
};
use PHPUnit\Framework\TestCase;

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
        $values = \iterator_to_array($elements->values());

        $this->assertCount(100, $values);
    }

    public function testTake()
    {
        $elements = Elements::of(...range(0, 1000));
        $elements2 = $elements->take(10);
        $aValues = \iterator_to_array($elements->values());
        $bValues = \iterator_to_array($elements2->values());

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
                \iterator_to_array($elements2->values()),
                $containsEvenInt,
                false,
            ),
        );
        $this->assertTrue(
            \array_reduce(
                \iterator_to_array($elements->values()),
                $containsEvenInt,
                false,
            ),
        );
    }

    public function testValues()
    {
        $elements = Elements::of(...range(0, 1000));

        $this->assertInstanceOf(\Generator::class, $elements->values());
        $this->assertCount(100, \iterator_to_array($elements->values()));
    }
}
