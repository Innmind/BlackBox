<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Either,
    Set,
    Set\Value,
};

class EitherTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new Either(
                $this->createMock(Set::class),
                $this->createMock(Set::class),
            ),
        );
    }

    public function testTake100ValuesByDefault()
    {
        $either = new Either(
            Set\Elements::of(1),
            Set\Elements::of(2),
        );

        $this->assertInstanceOf(\Generator::class, $either->values());
        $this->assertCount(100, $this->unwrap($either->values()));
        $values = \array_values(\array_unique($this->unwrap($either->values())));
        \sort($values);
        $this->assertSame([1, 2], $values);
    }

    public function testTake()
    {
        $either1 = new Either(
            Set\Elements::of(1),
            Set\Elements::of(2),
        );
        $either2 = $either1->take(50);

        $this->assertNotSame($either1, $either2);
        $this->assertInstanceOf(Either::class, $either2);
        $this->assertCount(100, $this->unwrap($either1->values()));
        $this->assertCount(50, $this->unwrap($either2->values()));
    }

    public function testFilter()
    {
        $either1 = new Either(
            Set\Elements::of(1),
            Set\Elements::of(null),
            Set\Elements::of(2),
        );
        $either2 = $either1->filter(static function(?int $value): bool {
            return $value === 1;
        });

        $this->assertNotSame($either1, $either2);
        $this->assertInstanceOf(Either::class, $either2);
        $this->assertCount(100, $this->unwrap($either1->values()));
        $this->assertCount(100, $this->unwrap($either2->values()));
        $values1 = \array_values(\array_unique($this->unwrap($either1->values())));
        \sort($values1);
        $this->assertSame([null, 1, 2], $values1);
        $values2 = \array_values(\array_unique($this->unwrap($either2->values())));
        $this->assertSame([1], $values2);
    }

    public function testValues()
    {
        $set = new Either(
            Set\Elements::of(1),
            Set\Elements::of(null),
            Set\Elements::of(2),
        );

        foreach ($set->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }
}
