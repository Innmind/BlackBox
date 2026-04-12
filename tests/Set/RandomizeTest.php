<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
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
            Set::of('')->randomize(),
        );
    }

    public function testGenerate100ValuesByDefault()
    {
        $set = Set::of(new \stdClass, 42)->randomize();

        $this->assertCount(100, $this->unwrap($set->values(Random::mersenneTwister)));
    }

    public function testTake()
    {
        $set1 = Set::of(new \stdClass, 42)->randomize();
        $set2 = $set1->take(50);

        $this->assertInstanceOf(Set::class, $set2);
        $this->assertNotSame($set2, $set1);
        $this->assertCount(100, $this->unwrap($set1->values(Random::mersenneTwister)));
        $this->assertCount(50, $this->unwrap($set2->values(Random::mersenneTwister)));
    }

    public function testFilter()
    {
        $set1 = Set::of('foo', 42)->randomize();
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
        $expected = new \stdClass;
        $set = Set::generator(static fn() => yield $expected)->randomize();

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $this->assertSame($expected, $value->unwrap());
        }
    }

    public function testAlwaysReturnAValueEvenWhenTheUnderlyingSetMayNotBeAbleToGenerateAnyValue()
    {
        $set = Set::generator(static function() {
            if (\mt_rand(0, 1) === 1) {
                yield \mt_rand();
            }
        })->randomize();

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
        }
    }
}
