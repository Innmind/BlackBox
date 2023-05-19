<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Randomize,
    Set,
    Set\Value,
    Random,
};

class RandomizeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            Randomize::of($this->createMock(Set::class)),
        );
    }

    public function testGenerate100ValuesByDefault()
    {
        $set = Randomize::of(
            Set\Elements::of(new \stdClass, 42),
        );

        $this->assertCount(100, $this->unwrap($set->values(Random::mersenneTwister)));
    }

    public function testTake()
    {
        $set1 = Randomize::of(
            Set\Elements::of(new \stdClass, 42),
        );
        $set2 = $set1->take(50);

        $this->assertInstanceOf(Set::class, $set2);
        $this->assertNotSame($set2, $set1);
        $this->assertCount(100, $this->unwrap($set1->values(Random::mersenneTwister)));
        $this->assertCount(50, $this->unwrap($set2->values(Random::mersenneTwister)));
    }

    public function testFilter()
    {
        $set1 = Randomize::of(
            Set\Elements::of('foo', 42),
        );
        $set2 = $set1->filter(static fn($v) => \is_int($v));

        $this->assertInstanceOf(Set::class, $set2);
        $this->assertNotSame($set2, $set1);
        $this->assertCount(100, $this->unwrap($set1->values(Random::mersenneTwister)));
        $this->assertCount(100, $this->unwrap($set2->values(Random::mersenneTwister)));
        $this->assertCount(
            2,
            \array_unique($this->unwrap($set1->values(Random::mersenneTwister))),
        );
        $this->assertCount(
            1,
            \array_unique($this->unwrap($set2->values(Random::mersenneTwister))),
        );
    }

    public function testAlwaysTakeTheFirstValueGeneratedByTheUnderlyingSet()
    {
        $set = Randomize::of(
            $inner = $this->createMock(Set::class),
        );
        $expected = Value::immutable(new \stdClass);
        $inner
            ->expects($this->exactly(100))
            ->method('values')
            ->willReturn((static fn() => yield $expected)());

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $this->assertSame($expected, $value);
        }
    }

    public function testAlwaysReturnAValueEvenWhenTheUnderlyingSetMayNotBeAbleToGenerateAnyValue()
    {
        $set = Randomize::of(Set\FromGenerator::of(static function() {
            if (\mt_rand(0, 1) === 1) {
                yield \mt_rand();
            }
        }));

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
        }
    }
}
