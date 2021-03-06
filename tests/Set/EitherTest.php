<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Either,
    Set,
    Set\Value,
    Random\MtRand,
    Exception\EmptySet,
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

        $this->assertInstanceOf(\Generator::class, $either->values(new MtRand));
        $this->assertCount(100, $this->unwrap($either->values(new MtRand)));
        $values = \array_values(\array_unique($this->unwrap($either->values(new MtRand))));
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
        $this->assertCount(100, $this->unwrap($either1->values(new MtRand)));
        $this->assertCount(50, $this->unwrap($either2->values(new MtRand)));
    }

    public function testFilter()
    {
        $either = new Either(
            Set\Elements::of(1),
            Set\Elements::of(null),
            Set\Elements::of(2),
        );

        $either2 = $either->filter(static function(?int $value): bool {
            return $value === 1;
        });

        $this->assertNotSame($either, $either2);
        $this->assertInstanceOf(Either::class, $either2);

        $this->assertSame([1], \array_unique($this->unwrap($either2->values(new MtRand))));
        $unique = \array_unique($this->unwrap($either->values(new MtRand)));
        \sort($unique);
        $this->assertSame([null, 1, 2], $unique);
    }

    public function testValues()
    {
        $set = new Either(
            Set\Elements::of(1),
            Set\Elements::of(null),
            Set\Elements::of(2),
        );

        foreach ($set->values(new MtRand) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testAlwaysReturnAValueEvenWhenTheUnderlyingSetMayNotBeAbleToGenerateAnyValue()
    {
        $set = new Either(
            Set\FromGenerator::of(static function() {
                if (\mt_rand(0, 1) === 1) {
                    yield \mt_rand();
                }
            }),
            Set\Elements::of(2),
        );

        foreach ($set->values(new MtRand) as $value) {
            $this->assertInstanceOf(Value::class, $value);
        }
    }

    public function testAlwaysUseAnotherSetWhenOneIsAnEmptySet()
    {
        $set = new Either(
            Set\Elements::of(1)->filter(static fn() => false),
            Set\Elements::of(2),
        );

        foreach ($set->values(new MtRand) as $value) {
            $this->assertSame(2, $value->unwrap());
        }
    }

    public function testThrowWhenNoValueCanBeGenerated()
    {
        $set = new Either(
            Set\Elements::of(1)->filter(static fn() => false),
            Set\Elements::of(2),
        );

        $this->expectException(EmptySet::class);

        $set
            ->filter(static fn() => false)
            ->values(new MtRand)
            ->current();
    }
}
