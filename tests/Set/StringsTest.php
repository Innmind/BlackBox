<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Strings,
    Set\Regex,
    Set\Chars,
    Set,
    Set\Value,
    Random\MtRand,
};
use ReverseRegex\Lexer;

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
        if (!\class_exists(Lexer::class)) {
            $this->markTestSkipped();
        }

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
            if (!$value->shrinkable()) {
                continue;
            }

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
        $strings = Strings::any()->filter(static fn($string) => $string !== '');

        foreach ($strings->values(new MtRand) as $value) {
            $this->assertTrue($value->shrinkable());
        }
    }

    public function testShrinkedValuesAreImmutable()
    {
        $strings = Strings::any()->filter(static fn($string) => $string !== '');

        foreach ($strings->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertTrue($a->isImmutable());
            $this->assertTrue($b->isImmutable());
        }
    }

    public function testShrinkedValuesAlwaysMatchTheGivenPredicate()
    {
        $strings = Strings::any()->filter(static fn($string) => \strlen($string) > 20);

        foreach ($strings->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();

            $this->assertTrue(\strlen($dichotomy->a()->unwrap()) > 20);
            $this->assertTrue(\strlen($dichotomy->b()->unwrap()) > 20);
        }
    }

    public function testBetween()
    {
        $strings = Strings::between(100, 200);

        foreach ($strings->values(new MtRand) as $value) {
            $this->assertGreaterThanOrEqual(100, \strlen($value->unwrap()));
            $this->assertLessThanOrEqual(200, \strlen($value->unwrap()));
        }
    }

    public function testAtLeast()
    {
        $strings = Strings::atLeast(100);

        foreach ($strings->values(new MtRand) as $value) {
            $this->assertGreaterThanOrEqual(100, \strlen($value->unwrap()));
        }
    }

    public function testInitialBoundsAreAlwaysRespectedWhenShrinking()
    {
        $integers = Strings::between(20, 80);

        $assertInBounds = function(Value $value, string $strategy) {
            while ($value->shrinkable()) {
                $this->assertGreaterThanOrEqual(20, \strlen($value->unwrap()));
                $this->assertLessThanOrEqual(80, \strlen($value->unwrap()));
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

    public function testMadeOf()
    {
        $set = Strings::madeOf(Chars::lowercaseLetter());
        $allowed = \range('a', 'z');

        foreach ($set->values(new MtRand) as $value) {
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

        foreach ($set->values(new MtRand) as $value) {
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

        foreach ($set->values(new MtRand) as $value) {
            $string = $value->unwrap();

            $this->assertGreaterThan(1, \strlen($string));
            $this->assertLessThan(43, \strlen($string));
        }
    }

    public function testMadeOfAtLeast()
    {
        $set = Strings::madeOf(Chars::any())->atLeast(2);

        foreach ($set->values(new MtRand) as $value) {
            $string = $value->unwrap();

            $this->assertGreaterThan(1, \strlen($string));
            $this->assertLessThan(131, \strlen($string));
        }
    }

    public function testMadeOfAtMost()
    {
        $set = Strings::madeOf(Chars::any())->atMost(42);

        foreach ($set->values(new MtRand) as $value) {
            $string = $value->unwrap();

            $this->assertGreaterThanOrEqual(0, \strlen($string));
            $this->assertLessThan(43, \strlen($string));
        }
    }

    public function testFilterMadeOf()
    {
        $set = Strings::madeOf(Chars::any())->filter(static fn($string) => $string !== '');

        foreach ($set->values(new MtRand) as $value) {
            $this->assertNotSame('', $value->unwrap());
        }
    }

    public function testTakeMadeOf()
    {
        $set = Strings::madeOf(Chars::any());
        $set2 = $set->take(50);

        $this->assertCount(100, \iterator_to_array($set->values(new MtRand)));
        $this->assertCount(50, \iterator_to_array($set2->values(new MtRand)));
    }
}
