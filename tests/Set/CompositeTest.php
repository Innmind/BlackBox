<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Set\Value,
    Random,
    PHPUnit\BlackBox,
    Runner\Proof\Scenario\Failure,
};
use PHPUnit\Framework\Attributes\Group;

class CompositeTest extends TestCase
{
    use BlackBox;

    private $set;

    public function setUp(): void
    {
        $this->set = Set::compose(
            static function(string ...$args) {
                return \implode('', $args);
            },
            Set::of('e', 'f'),
            Set::of('a', 'b'),
            Set::of('c', 'd'),
        );
    }

    public function testTake()
    {
        $values = $this->unwrap($this->set->take(500)->values(Random::mersenneTwister));

        $this
            ->assert()
            ->count(500, $values);
        $this
            ->assert()
            ->array($values)
            ->contains('eac')
            ->contains('ead');
    }

    public function testFilter()
    {
        $values = $this
            ->set
            ->filter(static function(string $value): bool {
                return $value[0] === 'e';
            });

        $this
            ->assert()
            ->array($this->unwrap($values->values(Random::mersenneTwister)))
            ->contains('eac')
            ->contains('ead')
            ->contains('ebc')
            ->contains('ebd')
            ->not()
            ->contains('fac')
            ->contains('fad')
            ->contains('fbc')
            ->contains('fbd');
    }

    public function testReduce()
    {
        $values = $this->unwrap($this->set->toSet()->values(Random::mersenneTwister));

        $this
            ->assert()
            ->array($values)
            ->contains('eac')
            ->contains('ead')
            ->contains('ebc')
            ->contains('ebd')
            ->contains('fac')
            ->contains('fad')
            ->contains('fbc')
            ->contains('fbd');
    }

    public function testValues()
    {
        $this->assertInstanceOf(\Generator::class, $this->set->toSet()->values(Random::mersenneTwister));
        $this->assertCount(100, $this->unwrap($this->set->toSet()->values(Random::mersenneTwister)));

        foreach ($this->set->toSet()->values(Random::mersenneTwister) as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->immutable());
        }
    }

    public function testGeneratedValueIsDeclaredMutableWhenSaidByTheSet()
    {
        $set = Set::compose(
            static function(string ...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            Set::generator(static function() {
                yield 'e';
                yield 'f';
            }),
            Set::generator(static function() {
                yield 'a';
                yield 'b';
            }),
            Set::generator(static function() {
                yield 'c';
                yield 'd';
            }),
        )->mutable()->toSet();

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $this->assertFalse($value->immutable());
            $this->assertNotSame($value->unwrap(), $value->unwrap());
            $this->assertSame($value->unwrap()->prop, $value->unwrap()->prop);
        }
    }

    public function testGeneratedValueIsDeclaredMutableWhenUnderlyingSetIsMutableEvenThoughOurSetIsDeclaredImmutable()
    {
        $set = Set::compose(
            static function(object $value, string $char) {
                $std = new \stdClass;
                $std->prop = $value;
                $std->char = $char;

                return $std;
            },
            Set::decorate(
                static function(string $value) {
                    $std = new \stdClass;
                    $std->prop = $value;

                    return $std;
                },
                Set::generator(static function() {
                    yield 'ea';
                    yield 'fb';
                    yield 'gc';
                    yield 'eb';
                }),
            ),
            Set::generator(static function() {
                yield 'c';
                yield 'd';
            }),
        )->filter(static fn($object) => $object->prop->prop[0] === 'e');

        $this->assertCount(100, \iterator_to_array($set->values(Random::mersenneTwister)));

        foreach ($set->values(Random::mersenneTwister) as $value) {
            $this->assertFalse($value->immutable());
            $this->assertNotSame($value->unwrap(), $value->unwrap());
            $this->assertNotSame($value->unwrap()->prop, $value->unwrap()->prop);
            $this->assertSame($value->unwrap()->prop->prop, $value->unwrap()->prop->prop);
            $this->assertSame($value->unwrap()->char, $value->unwrap()->char);
        }
    }

    public function testConservesMutabilityFromUnderlyingSets()
    {
        $mutable = Set::compose(
            static function(string ...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            Set::generator(static function() {
                yield 'e';
                yield 'f';
            }),
            Set::generator(static function() {
                yield 'a';
                yield 'b';
            }),
            Set::generator(static function() {
                yield 'c';
                yield 'd';
            }),
        )->mutable()->toSet();

        foreach ($mutable->values(Random::mersenneTwister) as $value) {
            $this->assertFalse($value->immutable());
        }

        $immutable = Set::compose(
            static function(int ...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            Set::integers(),
            Set::integers(),
            Set::integers(),
        );

        foreach ($immutable->toSet()->values(Random::mersenneTwister) as $value) {
            $this->assertTrue($value->immutable());
        }
    }

    public function testShrinkableAsLongAsOneUnderlyingSetIs()
    {
        $shrinkable = Set::compose(
            static function(...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            Set::generator(static function() {
                yield 'e';
                yield 'f';
            }),
            Set::integers(),
            Set::generator(static function() {
                yield 'c';
                yield 'd';
            }),
        );

        foreach ($shrinkable->toSet()->values(Random::mersenneTwister) as $value) {
            $this->assertNotNull($value->shrink());
        }

        $nonShrinkable = Set::compose(
            static function(string ...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            Set::generator(static function() {
                yield 'e';
                yield 'f';
            }),
            Set::generator(static function() {
                yield 'a';
                yield 'b';
            }),
            Set::generator(static function() {
                yield 'c';
                yield 'd';
            }),
        );

        foreach ($nonShrinkable->toSet()->values(Random::mersenneTwister) as $value) {
            $this->assertNull($value->shrink());
        }
    }

    public function testShrinkedValuesUseTheDifferentStrategiesFromTheUnderlyingSets()
    {
        $set = Set::compose(
            static function(...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            Set::integers(),
            Set::integers(),
            Set::integers(),
        );

        foreach ($set->toSet()->values(Random::mersenneTwister) as $value) {
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
        $set = Set::compose(
            static function(string ...$args) {
                return \implode('', $args);
            },
            Set::strings()->between(0, 5),
            Set::strings()->between(0, 5),
        );

        foreach ($set->toSet()->values(Random::mersenneTwister) as $value) {
            $a = $value;

            while ($shrunk = $a->shrink()) {
                $a = $shrunk->a();
            }

            $this->assertSame('', $a->unwrap());
        }
    }

    /**
     * This test is here to help fix the problem described in the issue linked below
     *
     * Do not run this test in the CI as it fails regularly when coverage is
     * enabled. This is obviously not the correct solution but it will do until
     * the shrinking mechanism is improved and better tested.
     *
     * @see https://github.com/Innmind/BlackBox/issues/6
     */
    #[Group('local')]
    public function testShrinksAsFastAsPossible()
    {
        try {
            $this
                ->forAll(Set::integers()->below(0), Set::integers()->above(0))
                ->filter(static fn($a, $b) => $a !== 0)
                ->then(function($a, $b) {
                    $this->assertGreaterThanOrEqual(
                        0,
                        $a + $b,
                        "[$a,$b]",
                    );
                });
            $this->fail('The assertion should fail');
        } catch (Failure $e) {
            $this->assertStringContainsString(
                '[-1,0]',
                $e->assertion()->kind()->message(),
            );
        }
    }
}
