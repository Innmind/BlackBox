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
    Exception\EmptySet,
};

class CompositeTest extends TestCase
{
    private $set;

    public function setUp(): void
    {
        $this->set = Composite::immutable(
            function(string ...$args) {
                return implode('', $args);
            },
            FromGenerator::of(function() {
                yield 'e';
                yield 'f';
            }),
            FromGenerator::of(function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(function() {
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
                function() {},
                FromGenerator::of(function() {
                    yield 'e';
                    yield 'f';
                }),
                FromGenerator::of(function() {
                    yield 'a';
                    yield 'b';
                }),
                FromGenerator::of(function() {
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
            function(string ...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            FromGenerator::of(function() {
                yield 'e';
                yield 'f';
            }),
            FromGenerator::of(function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(function() {
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
            function(object $value, string $char) {
                $std = new \stdClass;
                $std->prop = $value;
                $std->char = $char;

                return $std;
            },
            Decorate::mutable(
                function(string $value) {
                    $std = new \stdClass;
                    $std->prop = $value;

                    return $std;
                },
                FromGenerator::of(function() {
                    yield 'ea';
                    yield 'fb';
                    yield 'gc';
                    yield 'eb';
                }),
            ),
            FromGenerator::of(function() {
                yield 'c';
                yield 'd';
            }),
        )->filter(fn($object) => $object->prop->prop[0] === 'e');

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
            function(string ...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            FromGenerator::of(function() {
                yield 'e';
                yield 'f';
            }),
            FromGenerator::of(function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(function() {
                yield 'c';
                yield 'd';
            }),
        );

        foreach ($mutable->values(new MtRand) as $value) {
            $this->assertFalse($value->isImmutable());
        }

        $immutable = Composite::immutable(
            function(int ...$args) {
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
            function(...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            FromGenerator::of(function() {
                yield 'e';
                yield 'f';
            }),
            Set\Integers::any(),
            FromGenerator::of(function() {
                yield 'c';
                yield 'd';
            }),
        );

        foreach ($shrinkable->values(new MtRand) as $value) {
            $this->assertTrue($value->shrinkable());
        }

        $nonShrinkable = Composite::immutable(
            function(string ...$args) {
                $std = new \stdClass;
                $std->prop = $args;

                return $std;
            },
            FromGenerator::of(function() {
                yield 'e';
                yield 'f';
            }),
            FromGenerator::of(function() {
                yield 'a';
                yield 'b';
            }),
            FromGenerator::of(function() {
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
            function(...$args) {
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
            function(string ...$args) {
                return implode('', $args);
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
            ->filter(fn() => false)
            ->values(new MtRand)
            ->current();
    }
}
