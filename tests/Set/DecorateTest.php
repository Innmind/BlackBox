<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Decorate,
    Set\FromGenerator,
    Set,
    Set\Value,
    Random,
};

class DecorateTest extends TestCase
{
    private $set;

    public function setUp(): void
    {
        $this->set = Decorate::immutable(
            static function(string $value) {
                return [$value];
            },
            FromGenerator::of(static function() {
                yield 'ea';
                yield 'fb';
                yield 'gc';
                yield 'eb';
            }),
        );
    }

    public function testInterface()
    {
        $this->assertInstanceOf(Set::class, $this->set);
    }

    public function testImmutable()
    {
        $this->assertInstanceOf(
            Set::class,
            Decorate::immutable(
                static function() {},
                FromGenerator::of(static function() {
                    yield 'e';
                    yield 'f';
                }),
            ),
        );
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
        $values = Decorate::immutable(
            static fn($value) => [$value],
            $this->set,
        )->filter(static fn($value) => $value[0][0][0] === 'e');

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
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testGeneratedValueIsDeclaredMutableWhenSaidByTheSet()
    {
        $set = Decorate::mutable(
            static function(string $value) {
                $std = new \stdClass;
                $std->prop = $value;

                return $std;
            },
            FromGenerator::of(static function() {
                yield 'ea';
                yield 'fb';
                yield 'gc';
                yield 'eb';
            }),
        );

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $this->assertFalse($value->isImmutable());
            $this->assertNotSame($value->unwrap(), $value->unwrap());
            $this->assertSame($value->unwrap()->prop, $value->unwrap()->prop);
        }
    }

    public function testGeneratedValueIsDeclaredMutableWhenUnderlyingSetIsMutableEvenThoughOurSetIsDeclaredImmutable()
    {
        $set = Decorate::immutable(
            static function(object $value) {
                $std = new \stdClass;
                $std->prop = $value;

                return $std;
            },
            Decorate::mutable(
                static function(string $value) {
                    $std = new \stdClass;
                    $std->prop = $value;

                    return $std;
                },
                FromGenerator::of(static function() {
                    yield 'ea';
                    yield 'fb';
                    yield 'gc';
                    yield 'eb';
                }),
            ),
        )->filter(static fn($object) => $object->prop->prop[0] === 'e');

        $this->assertCount(2, \iterator_to_array($set->values(Random::mersenneTwister)));

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $this->assertFalse($value->isImmutable());
            $this->assertNotSame($value->unwrap(), $value->unwrap());
            $this->assertNotSame($value->unwrap()->prop, $value->unwrap()->prop);
            $this->assertSame($value->unwrap()->prop->prop, $value->unwrap()->prop->prop);
        }
    }

    public function testConserveUnderlyingSetShrinkability()
    {
        $nonShrinkable = Decorate::immutable(
            static function(string $value) {
                return [$value];
            },
            FromGenerator::of(static function() {
                yield 'ea';
                yield 'fb';
                yield 'gc';
                yield 'eb';
            }),
        );

        foreach ($nonShrinkable->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }

        $shrinkable = Decorate::immutable(
            static function(int $value) {
                return [$value];
            },
            Set\Integers::any(),
        );

        foreach ($shrinkable->values(Random::mersenneTwister) as $value) {
            $this->assertNotNull($value->shrink());
        }
    }

    public function testShrinkedValuesConserveMutability()
    {
        $mutable = Decorate::mutable(
            static function(int $value) {
                $std = new \stdClass;
                $std->prop = $value;

                return $std;
            },
            Set\Integers::any(),
        );

        foreach ($mutable->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            $this->assertFalse($dichotomy->a()->isImmutable());
            $this->assertFalse($dichotomy->b()->isImmutable());
        }

        $immutable = Decorate::immutable(
            static function(int $value) {
                return [$value];
            },
            Set\Integers::any(),
        );

        foreach ($immutable->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            $this->assertTrue($dichotomy->a()->isImmutable());
            $this->assertTrue($dichotomy->b()->isImmutable());
        }
    }

    public function testShrinkedValuesAlwaysRespectTheSetPredicate()
    {
        $set = Decorate::immutable(
            static function(int $value) {
                return [$value];
            },
            Set\Integers::any(),
        )->filter(static fn($v) => $v[0] % 2 === 0);

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            $this->assertSame(0, $dichotomy->a()->unwrap()[0] % 2);
            $this->assertSame(0, $dichotomy->b()->unwrap()[0] % 2);
        }
    }
}
