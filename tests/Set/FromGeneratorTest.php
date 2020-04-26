<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\FromGenerator,
    Set,
    Set\Value,
    Random\MtRand,
};

class FromGeneratorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new FromGenerator(function(){
                yield 42;
            }),
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(
            FromGenerator::class,
            FromGenerator::of(function() {
                yield 42;
            }),
        );
    }

    public function testThrowWhenTheCallableDoesntReturnAGenerator()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 1 must be of type callable(): \Generator');

        new FromGenerator(function(){});
    }

    public function testTake()
    {
        $a = FromGenerator::of(function() {
            foreach (range(0, 1000) as $i) {
                yield $i;
            }
        });
        $aValues = $this->unwrap($a->values(new MtRand));

        $b = $a->take(50);
        $bValues = $this->unwrap($b->values(new MtRand));

        $this->assertInstanceOf(FromGenerator::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $aValues);
        $this->assertCount(50, $bValues);
    }

    public function testFilter()
    {
        $a = FromGenerator::of(function() {
            foreach (range(0, 1000) as $i) {
                yield $i;
            }
        })->filter(static function(int $value): bool {
            return $value > 50;
        });
        $aValues = $this->unwrap($a->values(new MtRand));

        $b = $a->filter(static function(int $value): bool {
            return $value <= 100;
        });
        $bValues = $this->unwrap($b->values(new MtRand));

        $this->assertInstanceOf(FromGenerator::class, $a);
        $this->assertInstanceOf(FromGenerator::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $aValues);
        $this->assertCount(50, $bValues);
        $this->assertSame(51, min($aValues));
        $this->assertSame(150, max($aValues));
        $this->assertSame(51, min($bValues));
        $this->assertSame(100, max($bValues));
    }

    public function testStopsGeneratingValueWhenGeneratorRunsOut()
    {
        $a = FromGenerator::of(function() {
            foreach (range(0, 10) as $i) {
                yield $i;
            }
        });
        $aValues = $this->unwrap($a->values(new MtRand));

        $this->assertCount(11, $aValues);
    }

    public function testValues()
    {
        $a = FromGenerator::of(function() {
            foreach (\range(0, 10) as $i) {
                yield $i;
            }
        });

        $this->assertInstanceOf(\Generator::class, $a->values(new MtRand));
        $this->assertSame(\range(0, 10), $this->unwrap($a->values(new MtRand)));

        foreach ($a->values(new MtRand) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testGeneratedValuesAreNotShrinkable()
    {
        $generated = FromGenerator::of(function() {
            foreach (\range(0, 100) as $i) {
                yield $i;
            }
        });

        foreach ($generated->values(new MtRand) as $value) {
            $this->assertFalse($value->shrinkable());
        }
    }
}
