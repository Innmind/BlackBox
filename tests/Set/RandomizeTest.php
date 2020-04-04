<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Randomize,
    Set,
    Set\Value,
};

class RandomizeTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new Randomize($this->createMock(Set::class)),
        );
    }

    public function testGenerate100ValuesByDefault()
    {
        $set = new Randomize(
            Set\Elements::of(new \stdClass, 42),
        );

        $this->assertCount(100, $this->unwrap($set->values()));
    }

    public function testTake()
    {
        $set1 = new Randomize(
            Set\Elements::of(new \stdClass, 42),
        );
        $set2 = $set1->take(50);

        $this->assertInstanceOf(Set::class, $set2);
        $this->assertNotSame($set2, $set1);
        $this->assertCount(100, $this->unwrap($set1->values()));
        $this->assertCount(50, $this->unwrap($set2->values()));
    }

    public function testFilter()
    {
        $set1 = new Randomize(
            Set\Elements::of('foo', 42),
        );
        $set2 = $set1->filter(fn($v) => \is_int($v));

        $this->assertInstanceOf(Set::class, $set2);
        $this->assertNotSame($set2, $set1);
        $this->assertCount(100, $this->unwrap($set1->values()));
        $this->assertCount(100, $this->unwrap($set2->values()));
        $this->assertCount(
            2,
            \array_unique($this->unwrap($set1->values())),
        );
        $this->assertCount(
            1,
            \array_unique($this->unwrap($set2->values())),
        );
    }

    public function testAlwaysTakeTheFirstValueGeneratedByTheUnderlyingSet()
    {
        $set = new Randomize(
            $inner = $this->createMock(Set::class),
        );
        $expected = Value::immutable(new \stdClass);
        $inner
            ->expects($this->exactly(100))
            ->method('values')
            ->willReturn((fn() => yield $expected)());

        foreach ($set->values() as $value) {
            $this->assertSame($expected, $value);
        }
    }

    public function testAlwaysReturnAValueEvenWhenTheUnderlyingSetMayNotBeAbleToGenerateAnyValue()
    {
        $set = new Randomize(Set\FromGenerator::of(function() {
            if (mt_rand(0, 1) === 1) {
                yield mt_rand();
            }
        }));

        foreach ($set->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
        }
    }
}
