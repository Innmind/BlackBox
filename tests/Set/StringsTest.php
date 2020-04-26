<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Strings,
    Set\Regex,
    Set,
    Set\Value,
    Random\MtRand,
};

class StringsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, new Strings);
    }

    public function testAny()
    {
        $this->assertInstanceOf(Strings::class, Strings::any());
    }

    public function testMatching()
    {
        $this->assertInstanceOf(Regex::class, Strings::matching('\d'));
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Strings::any()->values(new MtRand));

        $this->assertCount(100, $values);
    }

    public function testByDefaultMaxLengthIs128()
    {
        $values = \array_map(
            static function(string $value): int {
                return \strlen($value);
            },
            $this->unwrap(Strings::any()->values(new MtRand)),
        );

        $this->assertLessThanOrEqual(128, \max($values));
    }

    public function testMaxLengthIsParametrable()
    {
        $values = \array_map(
            static function(string $value): int {
                return \strlen($value);
            },
            $this->unwrap(Strings::atMost(256)->values(new MtRand)),
        );

        $this->assertLessThanOrEqual(256, \max($values));
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Strings::any();
        $others = $values->filter(static function(string $value): bool {
            return \strlen($value) < 10;
        });

        $this->assertInstanceOf(Strings::class, $others);
        $this->assertNotSame($values, $others);
        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($values->values(new MtRand)),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertTrue($hasLengthAbove10);

        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($others->values(new MtRand)),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertFalse($hasLengthAbove10);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Strings::any();
        $b = $a->take(50);

        $this->assertInstanceOf(Strings::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(new MtRand)));
        $this->assertCount(50, $this->unwrap($b->values(new MtRand)));
    }

    public function testValues()
    {
        $a = Strings::any();

        $this->assertInstanceOf(\Generator::class, $a->values(new MtRand));
        $this->assertCount(100, $this->unwrap($a->values(new MtRand)));

        foreach ($a->values(new MtRand) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testEmptyStringCannotBeShrinked()
    {
        $strings = new Strings(1); // always generate string of length 1

        foreach ($strings->values(new MtRand) as $value) {
            $this->assertFalse(
                $value
                    ->shrink()
                    ->a() // length of 0
                    ->shrinkable()
            );
        }
    }

    public function testNonEmptyStringsAreShrinkable()
    {
        $strings = Strings::any()->filter(fn($string) => $string !== '');

        foreach ($strings->values(new MtRand) as $value) {
            $this->assertTrue($value->shrinkable());
        }
    }

    public function testShrinkedValuesAreImmutable()
    {
        $strings = Strings::any()->filter(fn($string) => $string !== '');

        foreach ($strings->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertTrue($a->isImmutable());
            $this->assertTrue($b->isImmutable());
        }
    }

    public function testStringsAreShrinkedFromBothEnds()
    {
        $strings = Strings::any()->filter(fn($string) => $string !== '');

        foreach ($strings->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            if (strlen($value->unwrap()) === 1) {
                // because it will shrink to the identity value because the shrunk
                // empty string wouldn't match the predicate
                continue;
            }

            $this->assertNotSame($a->unwrap(), $value->unwrap());
            $this->assertStringStartsWith($a->unwrap(), $value->unwrap());
            $this->assertNotSame($b->unwrap(), $value->unwrap());
            $this->assertStringEndsWith($b->unwrap(), $value->unwrap());
        }
    }

    public function testShrinkedValuesAlwaysMatchTheGivenPredicate()
    {
        $strings = Strings::any()->filter(fn($string) => strlen($string) > 20);

        foreach ($strings->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();

            $this->assertTrue(strlen($dichotomy->a()->unwrap()) > 20);
            $this->assertTrue(strlen($dichotomy->b()->unwrap()) > 20);
        }
    }

    public function testBetween()
    {
        $strings = Strings::between(100, 200);

        foreach ($strings->values(new MtRand) as $value) {
            $this->assertGreaterThanOrEqual(100, strlen($value->unwrap()));
            $this->assertLessThanOrEqual(200, strlen($value->unwrap()));
        }
    }

    public function testAtLeast()
    {
        $strings = Strings::atLeast(100);

        foreach ($strings->values(new MtRand) as $value) {
            $this->assertGreaterThanOrEqual(100, strlen($value->unwrap()));
        }
    }

    public function testInitialBoundsAreAlwaysRespectedWhenShrinking()
    {
        $integers = Strings::between(20, 80);

        $assertInBounds = function(Value $value, string $strategy) {
            while ($value->shrinkable()) {
                $this->assertGreaterThanOrEqual(20, mb_strlen($value->unwrap()));
                $this->assertLessThanOrEqual(80, mb_strlen($value->unwrap()));
                $value = $value->shrink()->$strategy();
            }
        };

        foreach ($integers->values(new MtRand) as $value) {
            $assertInBounds($value, 'a');
            $assertInBounds($value, 'b');
        }
    }

    public function testTakeNoElement()
    {
        $this->assertCount(
            0,
            Strings::any()
                ->take(0)
                ->values(new MtRand)
        );
    }
}
