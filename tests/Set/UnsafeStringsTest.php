<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\UnsafeStrings,
    Set,
    Set\Value,
    Random,
    Exception\EmptySet,
};

class UnsafeStringsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, UnsafeStrings::any());
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(UnsafeStrings::any()->values(Random::mersenneTwister));

        $this->assertCount(100, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = UnsafeStrings::any();
        $others = $values->filter(static function(string $value): bool {
            return \strlen($value) < 10;
        });

        $this->assertInstanceOf(Set::class, $others);
        $this->assertNotSame($values, $others);
        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($values->values(Random::mersenneTwister)),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertTrue($hasLengthAbove10);

        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($others->values(Random::mersenneTwister)),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertFalse($hasLengthAbove10);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = UnsafeStrings::any();
        $b = $a->take(50);

        $this->assertInstanceOf(Set::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));
        $this->assertCount(50, $this->unwrap($b->values(Random::mersenneTwister)));
    }

    public function testValues()
    {
        $a = UnsafeStrings::any();

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testEmptyStringCannotBeShrinked()
    {
        $strings = UnsafeStrings::any()->filter(static fn($string) => $string === '');

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }
    }

    public function testNonEmptyStringsAreShrinkable()
    {
        $strings = UnsafeStrings::any()->filter(static fn($string) => $string !== '');

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $this->assertNotNull($value->shrink());
        }
    }

    public function testShrinkedValuesAreImmutable()
    {
        $strings = UnsafeStrings::any()->filter(static fn($string) => $string !== '');

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertTrue($a->isImmutable());
            $this->assertTrue($b->isImmutable());
        }
    }

    public function testStringsAreShrinkedFromBothEnds()
    {
        $strings = UnsafeStrings::any()->filter(static fn($string) => \strlen($string) > 1);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            if (\strlen($value->unwrap()) === 2) {
                // we continue as the shrinked values won't match the set predicate
                continue;
            }

            $this->assertNotSame($a->unwrap(), $value->unwrap());
            $this->assertStringStartsWith($a->unwrap(), $value->unwrap());
            $this->assertNotSame($b->unwrap(), $value->unwrap());
            $this->assertStringEndsWith($b->unwrap(), $value->unwrap());
        }
    }

    public function testStringsOfOneCharacterShrinkToThemselves()
    {
        // otherwise they won't match the given predicate
        $strings = UnsafeStrings::any()->filter(static fn($string) => \strlen($string) === 1);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertSame($a->unwrap(), $value->unwrap());
            $this->assertSame($b->unwrap(), $value->unwrap());
            $this->assertNull($a->shrink());
            $this->assertNull($b->shrink());
        }
    }

    public function testShrinkedValuesAlwaysMatchTheGivenPredicate()
    {
        $strings = UnsafeStrings::any()->filter(static fn($string) => \strlen($string) > 20);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            $this->assertTrue(\strlen($dichotomy->a()->unwrap()) > 20);
            $this->assertTrue(\strlen($dichotomy->b()->unwrap()) > 20);
        }
    }

    public function testThrowWhenCannotFindAValue()
    {
        $this->expectException(EmptySet::class);

        UnsafeStrings::any()
            ->filter(static fn() => false)
            ->values(Random::mersenneTwister)
            ->current();
    }
}
