<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\FromGenerator,
    Set,
};
use PHPUnit\Framework\TestCase;

class FromGeneratorTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            new FromGenerator('foo', function(){
                yield 42;
            })
        );
    }

    public function testOf()
    {
        $this->assertInstanceOf(
            FromGenerator::class,
            FromGenerator::of('foo', function() {
                yield 42;
            })
        );
    }

    public function testThrowWhenTheCallableDoesntReturnAGenerator()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 2 must be of type callable(): \Generator');

        new FromGenerator('foo', function(){});
    }

    public function testName()
    {
        $this->assertSame(
            'foo',
            FromGenerator::of('foo', function() {
                yield 42;
            })->name()
        );
    }

    public function testTake()
    {
        $a = FromGenerator::of('foo', function() {
            foreach (range(0, 1000) as $i) {
                yield $i;
            }
        });
        $aValues = $a->reduce(
            [],
            static function(array $values, int $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $b = $a->take(50);
        $bValues = $b->reduce(
            [],
            static function(array $values, int $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $this->assertInstanceOf(FromGenerator::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $aValues);
        $this->assertCount(50, $bValues);
    }

    public function testFilter()
    {
        $a = FromGenerator::of('foo', function() {
            foreach (range(0, 1000) as $i) {
                yield $i;
            }
        })->filter(static function(int $value): bool {
            return $value > 50;
        });
        $aValues = $a->reduce(
            [],
            static function(array $values, int $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $b = $a->filter(static function(int $value): bool {
            return $value <= 100;
        });
        $bValues = $b->reduce(
            [],
            static function(array $values, int $value): array {
                $values[] = $value;

                return $values;
            }
        );

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
        $a = FromGenerator::of('foo', function() {
            foreach (range(0, 10) as $i) {
                yield $i;
            }
        });
        $aValues = $a->reduce(
            [],
            static function(array $values, int $value): array {
                $values[] = $value;

                return $values;
            }
        );

        $this->assertCount(11, $aValues);
    }
}
