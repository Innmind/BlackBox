<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Collapse,
    Set,
    Set\Value,
    Random,
};

class StringsTest extends TestCase
{
    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Set::strings()->toSet()->values(Random::mersenneTwister));

        $this->assertCount(100, $values);
    }

    public function testByDefaultMaxLengthIs128()
    {
        $values = \array_map(
            static function(string $value): int {
                return \strlen($value);
            },
            $this->unwrap(Set::strings()->toSet()->values(Random::mersenneTwister)),
        );

        $this->assertLessThanOrEqual(128, \max($values));
    }

    public function testMaxLengthIsParametrable()
    {
        $values = \array_map(
            static function(string $value): int {
                return \strlen($value);
            },
            $this->unwrap(Set::strings()->atMost(256)->values(Random::mersenneTwister)),
        );

        $this->assertLessThanOrEqual(256, \max($values));
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Set::strings()->toSet();
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
        $a = Set::strings()->toSet();
        $b = $a->take(50);

        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));
        $this->assertCount(50, $this->unwrap($b->values(Random::mersenneTwister)));
    }

    public function testValues()
    {
        $a = Set::strings()->toSet();

        $this->assertInstanceOf(\Generator::class, $a->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($a->values(Random::mersenneTwister)));

        foreach ($a->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->immutable());
        }
    }

    public function testEmptyStringCannotBeShrinked()
    {
        $strings = Set::strings()->between(0, 1); // always generate string of length 1

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
        $strings = Set::strings()->toSet();

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            if ($value->unwrap() === '') {
                continue;
            }

            $this->assertNotNull($value->shrink());
        }
    }

    public function testShrinkedValuesAreImmutable()
    {
        $strings = Set::strings()->toSet();

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
        $strings = Set::strings()->filter(static fn($string) => \strlen($string) > 20);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            $this->assertTrue(\strlen($dichotomy->a()->unwrap()) > 20);
            $this->assertTrue(\strlen($dichotomy->b()->unwrap()) > 20);
        }
    }

    public function testBetween()
    {
        $strings = Set::strings()->between(100, 200);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $this->assertGreaterThanOrEqual(100, \strlen($value->unwrap()));
            $this->assertLessThanOrEqual(200, \strlen($value->unwrap()));
        }
    }

    public function testAtLeast()
    {
        $strings = Set::strings()->atLeast(100);

        foreach ($strings->values(Random::mersenneTwister) as $value) {
            $this->assertGreaterThanOrEqual(100, \strlen($value->unwrap()));
        }
    }

    public function testInitialBoundsAreAlwaysRespectedWhenShrinking()
    {
        $integers = Set::strings()->between(20, 80);

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

    public function testMadeOf()
    {
        $set = Set::strings()->madeOf(Set::strings()->chars()->lowercaseLetter());
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

        $set = Set::strings()->madeOf(Set::strings()->chars()->lowercaseLetter(), Set::strings()->chars()->uppercaseLetter());
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
        $set = Set::strings()->madeOf(Set::strings()->chars())->between(2, 42);

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $string = $value->unwrap();

            $this->assertGreaterThan(1, \strlen($string));
            $this->assertLessThan(43, \strlen($string));
        }
    }

    public function testMadeOfAtLeast()
    {
        $set = Set::strings()->madeOf(Set::strings()->chars())->atLeast(2);

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $string = $value->unwrap();

            $this->assertGreaterThan(1, \strlen($string));
            $this->assertLessThan(131, \strlen($string));
        }
    }

    public function testMadeOfAtMost()
    {
        $set = Set::strings()->madeOf(Set::strings()->chars())->atMost(42);

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $string = $value->unwrap();

            $this->assertGreaterThanOrEqual(0, \strlen($string));
            $this->assertLessThan(43, \strlen($string));
        }
    }

    public function testFilterMadeOf()
    {
        $set = Set::strings()->madeOf(Set::strings()->chars())->filter(static fn($string) => $string !== '');

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $this->assertNotSame('', $value->unwrap());
        }
    }

    public function testTakeMadeOf()
    {
        $set = Set::strings()->madeOf(Set::strings()->chars());
        $set2 = $set->take(50);

        $this->assertCount(100, \iterator_to_array(Collapse::of($set)->values(Random::mersenneTwister)));
        $this->assertCount(50, \iterator_to_array(Collapse::of($set2)->values(Random::mersenneTwister)));
    }
}
