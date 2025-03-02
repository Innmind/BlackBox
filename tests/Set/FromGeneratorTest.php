<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\FromGenerator,
    Set,
    Set\Value,
    Random,
    Exception\EmptySet,
};

class FromGeneratorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            FromGenerator::of(static function() {
                yield 42;
            }),
        );
    }

    public function testThrowWhenTheCallableDoesntReturnAGenerator()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 1 must be of type callable(): \Generator');

        FromGenerator::of(static function() {});
    }

    public function testTake()
    {
        $a = FromGenerator::of(static function() {
            foreach (\range(0, 1000) as $i) {
                yield $i;
            }
        });
        $aValues = $this->unwrap($a->values(Random::mersenneTwister));

        $b = $a->take(50);
        $bValues = $this->unwrap($b->values(Random::mersenneTwister));

        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $aValues);
        $this->assertCount(50, $bValues);
    }

    public function testFilter()
    {
        $a = FromGenerator::of(static function() {
            foreach (\range(0, 1000) as $i) {
                yield $i;
            }
        })->filter(static function(int $value): bool {
            return $value > 50;
        });
        $aValues = $this->unwrap($a->values(Random::mersenneTwister));

        $b = $a->filter(static function(int $value): bool {
            return $value <= 100;
        });
        $bValues = $this->unwrap($b->values(Random::mersenneTwister));

        $this->assertInstanceOf(Set::class, $a);
        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $aValues);
        $this->assertCount(50, $bValues);
        $this->assertSame(51, \min($aValues));
        $this->assertSame(150, \max($aValues));
        $this->assertSame(51, \min($bValues));
        $this->assertSame(100, \max($bValues));
    }

    public function testStopsGeneratingValueWhenGeneratorRunsOut()
    {
        $a = FromGenerator::of(static function() {
            foreach (\range(0, 10) as $i) {
                yield $i;
            }
        });
        $aValues = $this->unwrap($a->values(Random::mersenneTwister));

        $this->assertCount(11, $aValues);
    }

    public function testValues()
    {
        $a = FromGenerator::of(static function() {
            foreach (\range(0, 10) as $i) {
                yield $i;
            }
        });

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertSame(\range(0, 10), $this->unwrap($a->values(Random::mersenneTwister)));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testGeneratedValuesAreNotShrinkable()
    {
        $generated = FromGenerator::of(static function() {
            foreach (\range(0, 100) as $i) {
                yield $i;
            }
        });

        foreach ($generated->values(Random::mersenneTwister) as $value) {
            $this->assertFalse($value->shrinkable());
        }
    }

    public function testThrowWhenCannotFindAValue()
    {
        $generated = FromGenerator::of(static function() {
            foreach (\range(0, 100) as $i) {
                yield $i;
            }
        })->filter(static fn() => false);

        $this->expectException(EmptySet::class);

        $generated->values(Random::mersenneTwister)->current();
    }
}
