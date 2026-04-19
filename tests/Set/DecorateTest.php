<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Value,
    Random,
};

class DecorateTest extends TestCase
{
    private $set;

    public function setUp(): void
    {
        $this->set = Set::generator(static function() {
            yield 'ea';
            yield 'fb';
            yield 'gc';
            yield 'eb';
        })->map(static fn($value) => [$value]);
    }

    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, $this->set);
    }

    public function testTake()
    {
        $values = $this->unwrap($this->set->take(2)->values(Random::mersenneTwister));

        $this->assertSame(
            [
                ['ea'],
                ['fb'],
            ],
            $values,
        );
    }

    public function testFilter()
    {
        $values = $this
            ->set
            ->filter(static function(array $value): bool {
                return $value[0][0] === 'e';
            });

        $this->assertSame(
            [
                ['ea'],
                ['eb'],
            ],
            $this->unwrap($values->values(Random::mersenneTwister)),
        );
    }

    public function testFilteringDecoratedDecoratorIsAppliedCorrectly()
    {
        $values = $this
            ->set
            ->map(static fn($value) => [$value])
            ->filter(static fn($value) => $value[0][0][0] === 'e');

        $this->assertSame(
            [
                [['ea']],
                [['eb']],
            ],
            $this->unwrap($values->values(Random::mersenneTwister)),
        );
    }

    public function testReduce()
    {
        $values = $this->unwrap($this->set->values(Random::mersenneTwister));

        $this->assertSame(
            [
                ['ea'],
                ['fb'],
                ['gc'],
                ['eb'],
            ],
            $values,
        );
    }

    public function testValues()
    {
        $this->assertInstanceOf(\Generator::class, $this->set->values(Random::mersenneTwister));
        $this->assertCount(4, $this->unwrap($this->set->values(Random::mersenneTwister)));

        foreach ($this->set->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->immutable());
        }
    }

    public function testConserveUnderlyingSetShrinkability()
    {
        $nonShrinkable = Set::generator(static function() {
            yield 'ea';
            yield 'fb';
            yield 'gc';
            yield 'eb';
        })->map(static function(string $value) {
            return [$value];
        });

        foreach ($nonShrinkable->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }

        $shrinkable = Set::integers()->map(static function(int $value) {
            return [$value];
        });

        foreach ($shrinkable->values(Random::mersenneTwister) as $value) {
            $this->assertNotNull($value->shrink());
        }
    }

    public function testShrinkedValuesAlwaysRespectTheSetPredicate()
    {
        $set = Set::integers()
            ->map(static function(int $value) {
                return [$value];
            })
            ->filter(static fn($v) => $v[0] % 2 === 0);

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            if (\is_null($dichotomy)) {
                continue;
            }

            $this->assertSame(0, $dichotomy->a()->unwrap()[0] % 2);
            $this->assertSame(0, $dichotomy->b()->unwrap()[0] % 2);
        }
    }
}
