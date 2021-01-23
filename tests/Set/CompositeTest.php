<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Composite,
    Set\FromGenerator,
    Set\Decorate,
    Set,
    Set\Value,
    Random\MtRand,
    PHPUnit\BlackBox,
    Exception\EmptySet,
};
use PHPUnit\Framework\ExpectationFailedException;

class CompositeTest extends TestCase
{
    use BlackBox;

    private $set;

    public function setUp(): void
    {
        $this->set = Composite::immutable(
            static function(string ...$args) {
                return \implode('', $args);
            },
            FromGenerator::of(static function() {
                yield 'e';
                yield 'f';
            }),
            FromGenerator::of(static function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(static function() {
                yield 'c';
                yield 'd';
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
            Composite::class,
            Composite::immutable(
                static function() {},
                FromGenerator::of(static function() {
                    yield 'e';
                    yield 'f';
                }),
                FromGenerator::of(static function() {
                    yield 'a';
                    yield 'b';
                }),
                FromGenerator::of(static function() {
                    yield 'c';
                    yield 'd';
                }),
            ),
        );
    }

    public function testTake()
    {
        $values = $this->unwrap($this->set->take(2)->values(new MtRand));

        $this->assertSame(
            [
                'eac',
                'ead',
            ],
            $values,
        );
    }

    public function testFilter()
    {
        $values = $this
            ->set
            ->filter(static function(string $value): bool {
                return $value[0] === 'e';
            });

        $this->assertSame(
            [
                'eac',
                'ead',
                'ebc',
                'ebd',
            ],
            $this->unwrap($values->values(new MtRand)),
        );
    }

    public function testReduce()
    {
        $values = $this->unwrap($this->set->values(new MtRand));

        $this->assertSame(
            [
                'eac',
                'ead',
                'ebc',
                'ebd',
                'fac',
                'fad',
                'fbc',
                'fbd',
            ],
            $values,
        );
    }

    public function testValues()
    {
        $this->assertInstanceOf(\Generator::class, $this->set->values(new MtRand));
        $this->assertCount(8, $this->unwrap($this->set->values(new MtRand)));

        foreach ($this->set->values(new MtRand) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testGeneratedValueIsDeclaredMutableWhenSaidByTheSet()
    {
        $set = Composite::mutable(
            static function(string ...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            FromGenerator::of(static function() {
                yield 'e';
                yield 'f';
            }),
            FromGenerator::of(static function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(static function() {
                yield 'c';
                yield 'd';
            }),
        );

        foreach ($set->values(new MtRand) as $value) {
            $this->assertFalse($value->isImmutable());
            $this->assertNotSame($value->unwrap(), $value->unwrap());
            $this->assertSame($value->unwrap()->prop, $value->unwrap()->prop);
        }
    }

    public function testGeneratedValueIsDeclaredMutableWhenUnderlyingSetIsMutableEvenThoughOurSetIsDeclaredImmutable()
    {
        $set = Composite::immutable(
            static function(object $value, string $char) {
                $std = new \stdClass;
                $std->prop = $value;
                $std->char = $char;

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
            FromGenerator::of(static function() {
                yield 'c';
                yield 'd';
            }),
        )->filter(static fn($object) => $object->prop->prop[0] === 'e');

        $this->assertCount(4, \iterator_to_array($set->values(new MtRand)));

        foreach ($set->values(new MtRand) as $value) {
            $this->assertFalse($value->isImmutable());
            $this->assertNotSame($value->unwrap(), $value->unwrap());
            $this->assertNotSame($value->unwrap()->prop, $value->unwrap()->prop);
            $this->assertSame($value->unwrap()->prop->prop, $value->unwrap()->prop->prop);
            $this->assertSame($value->unwrap()->char, $value->unwrap()->char);
        }
    }

    public function testConservesMutabilityFromUnderlyingSets()
    {
        $mutable = Composite::mutable(
            static function(string ...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            FromGenerator::of(static function() {
                yield 'e';
                yield 'f';
            }),
            FromGenerator::of(static function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(static function() {
                yield 'c';
                yield 'd';
            }),
        );

        foreach ($mutable->values(new MtRand) as $value) {
            $this->assertFalse($value->isImmutable());
        }

        $immutable = Composite::immutable(
            static function(int ...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            Set\Integers::any(),
            Set\Integers::any(),
            Set\Integers::any(),
        );

        foreach ($immutable->values(new MtRand) as $value) {
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testShrinkableAsLongAsOneUnderlyingSetIs()
    {
        $shrinkable = Composite::immutable(
            static function(...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            FromGenerator::of(static function() {
                yield 'e';
                yield 'f';
            }),
            Set\Integers::any(),
            FromGenerator::of(static function() {
                yield 'c';
                yield 'd';
            }),
        );

        foreach ($shrinkable->values(new MtRand) as $value) {
            $this->assertTrue($value->shrinkable());
        }

        $nonShrinkable = Composite::immutable(
            static function(string ...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            FromGenerator::of(static function() {
                yield 'e';
                yield 'f';
            }),
            FromGenerator::of(static function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(static function() {
                yield 'c';
                yield 'd';
            }),
        );

        foreach ($nonShrinkable->values(new MtRand) as $value) {
            $this->assertFalse($value->shrinkable());
        }
    }

    public function testShrinkedValuesUseTheDifferentStrategiesFromTheUnderlyingSets()
    {
        $set = Composite::immutable(
            static function(...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            Set\Integers::any(),
            Set\Integers::any(),
            Set\Integers::any(),
        );

        foreach ($set->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();
            $value = $value->unwrap();
            $a = $dichotomy->a()->unwrap();
            $b = $dichotomy->b()->unwrap();

            $this->assertNotSame($a, $value);
            $this->assertNotSame($b, $value);
            $this->assertNotSame($a, $b);
            $this->assertNotSame($a->prop, $value->prop);
            $this->assertNotSame($b->prop, $value->prop);
            $this->assertNotSame($a->prop, $b->prop);
        }
    }

    public function testShrinkAllValuesToTheirMinimumPossible()
    {
        $set = Composite::immutable(
            static function(string ...$args) {
                return \implode('', $args);
            },
            Set\Strings::between(0, 5),
            Set\Strings::between(0, 5),
        );

        foreach ($set->values(new MtRand) as $value) {
            $a = $value;

            while ($a->shrinkable()) {
                $a = $a->shrink()->a();
            }

            $this->assertSame('', $a->unwrap());
        }
    }

    public function testThrowWhenUnableToGenerateValues()
    {
        $this->expectException(EmptySet::class);

        $this
            ->set
            ->filter(static fn() => false)
            ->values(new MtRand)
            ->current();
    }

    /**
     * This test is here to help fix the problem described in the issue linked below
     *
     * @see https://github.com/Innmind/BlackBox/issues/6
     */
    public function testShrinksAsFastAsPossible()
    {
        try {
            $this
                ->forAll(Set\Integers::below(0), Set\Integers::above(0))
                ->filter(fn($a, $b) => $a !== 0)
                ->then(function($a, $b) {
                    $this->assertGreaterThanOrEqual(
                        0,
                        $a + $b,
                        "[$a,$b]",
                    );
                });
            $this->fail('The assertion should fail');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                '[-1,0]',
                $e->getMessage(),
            );
        }
    }
}
