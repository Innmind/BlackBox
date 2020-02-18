<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Regex,
    Set,
    Set\Value,
};

class RegexTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, Regex::for('\d'));
    }

    public function testAny()
    {
        $this->assertInstanceOf(Regex::class, Regex::for('\d'));
    }

    public function testByDefault100ValuesAreGenerated()
    {
        $values = $this->unwrap(Regex::for('\d')->values());

        $this->assertCount(100, $values);
    }

    public function testPredicateIsAppliedOnReturnedSetOnly()
    {
        $values = Regex::for('[a-z]+');
        $others = $values->filter(static function(string $value): bool {
            return \strlen($value) < 10;
        });

        $this->assertInstanceOf(Regex::class, $others);
        $this->assertNotSame($values, $others);
        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($values->values()),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertTrue($hasLengthAbove10);

        $hasLengthAbove10 = \array_reduce(
            $this->unwrap($others->values()),
            static function(bool $hasLengthAbove10, string $value): bool {
                return $hasLengthAbove10 || \strlen($value) > 10;
            },
            false,
        );
        $this->assertFalse($hasLengthAbove10);
    }

    public function testSizeAppliedOnReturnedSetOnly()
    {
        $a = Regex::for('\d');
        $b = $a->take(50);

        $this->assertInstanceOf(Regex::class, $b);
        $this->assertNotSame($a, $b);
        $this->assertCount(100, $this->unwrap($a->values()));
        $this->assertCount(50, $this->unwrap($b->values()));
    }

    public function testValues()
    {
        $a = Regex::for('\d');

        $this->assertInstanceOf(\Generator::class, $a->values());
        $this->assertCount(100, $this->unwrap($a->values()));

        foreach ($a->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testNonEmptyStringsAreShrinkable()
    {
        $strings = Regex::for('[a-z]+');

        foreach ($strings->values() as $value) {
            if (strlen($value->unwrap()) === 1) {
                // because they can't be shrinked as they would no longer match
                // the pattern
                continue;
            }

            $this->assertTrue($value->shrinkable());
        }
    }

    public function testShrinkedValuesAreImmutable()
    {
        $strings = Regex::for('\d+');

        foreach ($strings->values() as $value) {
            if (strlen($value->unwrap()) === 1) {
                // because they can't be shrinked as they would no longer match
                // the pattern
                continue;
            }

            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertTrue($a->isImmutable());
            $this->assertTrue($b->isImmutable());
        }
    }

    public function testStringsAreShrinkedFromBothEnds()
    {
        $strings = Regex::for('[a-z][a-z]+');

        foreach ($strings->values() as $value) {
            if (strlen($value->unwrap()) === 2) {
                // as it will shrink to the identity value because a shorter value
                // wouldn't match the expression
                continue;
            }

            $dichotomy = $value->shrink();
            $a = $dichotomy->a();
            $b = $dichotomy->b();

            $this->assertNotSame($a->unwrap(), $value->unwrap());
            $this->assertStringStartsWith($a->unwrap(), $value->unwrap());
            $this->assertNotSame($b->unwrap(), $value->unwrap());
            $this->assertStringEndsWith($b->unwrap(), $value->unwrap());
        }
    }

    public function testShrinkedValuesAlwaysMatchTheGivenPredicate()
    {
        $strings = Regex::for('[a-z]+')->filter(fn($string) => strlen($string) > 20);

        foreach ($strings->values() as $value) {
            if (strlen($value->unwrap()) === 21) {
                // because they can't be shrinked as they would no longer match
                // the pattern
                continue;
            }

            $dichotomy = $value->shrink();

            $this->assertTrue(strlen($dichotomy->a()->unwrap()) > 20);
            $this->assertTrue(strlen($dichotomy->b()->unwrap()) > 20);
        }
    }

    public function testNeverGeneratedSameValueTwiceInARow()
    {
        $chars = Regex::for('[a-z]+')->values();
        $previous = $chars->current();
        $chars->next();

        while ($chars->valid()) {
            $this->assertNotSame($previous->unwrap(), $chars->current()->unwrap());
            $previous = $chars->current();
            $chars->next();
        }
    }

    public function testMinimumValueIsNotShrinkable()
    {
        $chars = Regex::for('[a-z]');

        foreach ($chars->values() as $char) {
            $this->assertFalse($char->shrinkable());
        }
    }
}
