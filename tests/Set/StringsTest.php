<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Strings,
    Set\Chars,
    Set\Collapse,
    Set,
    Set\Value,
    Random,
};

class StringsTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, Strings::any());
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Strings::any()->values(Random::mersenneTwister));

        $this->assertCount(100, $values);
    }

    public function testByDefaultMaxLengthIs128()
    {
        $values = \array_map(
            static function(string $value): int {
                return \strlen($value);
            },
            $this->unwrap(Strings::any()->values(Random::mersenneTwister)),
        );

        $this->assertLessThanOrEqual(128, \max($values));
    }

    public function testMaxLengthIsParametrable()
    {
        $values = \array_map(
            static function(string $value): int {
                return \strlen($value);
            },
            $this->unwrap(Strings::atMost(256)->values(Random::mersenneTwister)),
        );

        $this->assertLessThanOrEqual(256, \max($values));
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Strings::any();
        $others = $values->filter(static function(string $value): bool {
            return \strlen($value) < 10;
        });

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
        $a = Strings::any();
        $b = $a->take(50);

        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));
        $this->assertCount(50, $this->unwrap($b->values(Random::mersenneTwister)));
    }

    public function testValues()
    {
        $a = Strings::any();

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->immutable());
        }
    }

    public function testEmptyStringCannotBeShrinked()
    {
        $strings = Strings::between(0, 1); // always generate string of length 1

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            if (!$value->shrink()) {
                continue;
            }

            $this->assertNull(
                $value
                    ->shrink()
                    ->a() // length of 0
                    ->shrink(),
            );
        }
    }

    public function testNonEmptyStringsAreShrinkable()
    {
        $strings = Strings::any();

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            if ($value->unwrap() === '') {
                continue;
            }

            $this->assertNotNull($value->shrink());
        }
    }

    public function testShrinkedValuesAreImmutable()
    {
        $strings = Strings::any();

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            if ($value->unwrap() === '') {
                continue;
            }

            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertTrue($a->immutable());
            $this->assertTrue($b->immutable());
        }
    }

    public function testShrinkedValuesAlwaysMatchTheGivenPredicate()
    {
        $strings = Strings::any()->filter(static fn($string) => \strlen($string) > 20);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            $this->assertTrue(\strlen($dichotomy->a()->unwrap()) > 20);
            $this->assertTrue(\strlen($dichotomy->b()->unwrap()) > 20);
        }
    }

    public function testBetween()
    {
        $strings = Strings::between(100, 200);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $this->assertGreaterThanOrEqual(100, \strlen($value->unwrap()));
            $this->assertLessThanOrEqual(200, \strlen($value->unwrap()));
        }
    }

    public function testAtLeast()
    {
        $strings = Strings::atLeast(100);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $this->assertGreaterThanOrEqual(100, \strlen($value->unwrap()));
        }
    }

    public function testInitialBoundsAreAlwaysRespectedWhenShrinking()
    {
        $integers = Strings::between(20, 80);

        $assertInBounds = function(Value $value, string $strategy) {
            while ($shrunk = $value->shrink()) {
                $this->assertGreaterThanOrEqual(20, \strlen($value->unwrap()));
                $this->assertLessThanOrEqual(80, \strlen($value->unwrap()));
                $value = $shrunk->$strategy();
            }
        };

        foreach ($integers->values(Random::mersenneTwister) as $value) {
            $assertInBounds($value, 'a');
            $assertInBounds($value, 'b');
        }
    }

    public function testTakeNoElement()
    {
        $this->assertCount(
            0,
            \iterator_to_array(
                Strings::any()
                    ->take(0)
                    ->values(Random::mersenneTwister),
            ),
        );
    }

    public function testMadeOf()
    {
        $set = Strings::madeOf(Chars::lowercaseLetter());
        $allowed = \range('a', 'z');

        foreach (Collapse::of($set)->values(Random::mersenneTwister) as $value) {
            if (\strlen($value->unwrap()) === 0) {
                continue;
            }

            $chars = \str_split($value->unwrap());

            foreach ($chars as $char) {
                $this->assertContains($char, $allowed);
            }
        }

        $set = Strings::madeOf(Chars::lowercaseLetter(), Chars::uppercaseLetter());
        $allowed = [...\range('a', 'z'), ...\range('A', 'Z')];

        foreach (Collapse::of($set)->values(Random::mersenneTwister) as $value) {
            if (\strlen($value->unwrap()) === 0) {
                continue;
            }

            $chars = \str_split($value->unwrap());

            foreach ($chars as $char) {
                $this->assertContains($char, $allowed);
            }
        }
    }

    public function testMadeOfBetween()
    {
        $set = Strings::madeOf(Chars::any())->between(2, 42);

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $string = $value->unwrap();

            $this->assertGreaterThan(1, \strlen($string));
            $this->assertLessThan(43, \strlen($string));
        }
    }

    public function testMadeOfAtLeast()
    {
        $set = Strings::madeOf(Chars::any())->atLeast(2);

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $string = $value->unwrap();

            $this->assertGreaterThan(1, \strlen($string));
            $this->assertLessThan(131, \strlen($string));
        }
    }

    public function testMadeOfAtMost()
    {
        $set = Strings::madeOf(Chars::any())->atMost(42);

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $string = $value->unwrap();

            $this->assertGreaterThanOrEqual(0, \strlen($string));
            $this->assertLessThan(43, \strlen($string));
        }
    }

    public function testFilterMadeOf()
    {
        $set = Strings::madeOf(Chars::any())->filter(static fn($string) => $string !== '');

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $this->assertNotSame('', $value->unwrap());
        }
    }

    public function testTakeMadeOf()
    {
        $set = Strings::madeOf(Chars::any());
        $set2 = $set->take(50);

        $this->assertCount(100, \iterator_to_array(Collapse::of($set)->values(Random::mersenneTwister)));
        $this->assertCount(50, \iterator_to_array(Collapse::of($set2)->values(Random::mersenneTwister)));
    }
}
