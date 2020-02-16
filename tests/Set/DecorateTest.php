<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Decorate,
    Set\FromGenerator,
    Set,
    Set\Value,
};

class DecorateTest extends TestCase
{
    private $set;

    public function setUp(): void
    {
        $this->set = Decorate::immutable(
            function(string $value) {
                return [$value];
            },
            FromGenerator::of(function() {
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
            Decorate::class,
            Decorate::immutable(
                function() {},
                FromGenerator::of(function() {
                    yield 'e';
                    yield 'f';
                }),
            ),
        );
    }

    public function testTake()
    {
        $values = $this->unwrap($this->set->take(2)->values());

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
            $this->unwrap($values->values()),
        );
    }

    public function testReduce()
    {
        $values = $this->unwrap($this->set->values());

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
        $this->assertInstanceOf(\Generator::class, $this->set->values());
        $this->assertCount(4, $this->unwrap($this->set->values()));

        foreach ($this->set->values() as $value) {
            $this->assertInstanceOf(Value::class, $value);
            $this->assertTrue($value->isImmutable());
        }
    }

    public function testGeneratedValueIsDeclaredMutableWhenSaidByTheSet()
    {
        $set = Decorate::mutable(
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
        );

        foreach ($set->values() as $value) {
            $this->assertFalse($value->isImmutable());
            $this->assertNotSame($value->unwrap(), $value->unwrap());
            $this->assertSame($value->unwrap()->prop, $value->unwrap()->prop);
        }
    }

    public function testGeneratedValueIsDeclaredMutableWhenUnderlyingSetIsMutableEvenThoughOurSetIsDeclaredImmutable()
    {
        $set = Decorate::immutable(
            function(object $value) {
                $std = new \stdClass;
                $std->prop = $value;

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
            )
        )->filter(fn($object) => $object->prop->prop[0] === 'e');

        $this->assertCount(2, \iterator_to_array($set->values()));

        foreach ($set->values() as $value) {
            $this->assertFalse($value->isImmutable());
            $this->assertNotSame($value->unwrap(), $value->unwrap());
            $this->assertNotSame($value->unwrap()->prop, $value->unwrap()->prop);
            $this->assertSame($value->unwrap()->prop->prop, $value->unwrap()->prop->prop);
        }
    }

    public function testConserveUnderlyingSetShrinkability()
    {
        $nonShrinkable = Decorate::immutable(
            function(string $value) {
                return [$value];
            },
            FromGenerator::of(function() {
                yield 'ea';
                yield 'fb';
                yield 'gc';
                yield 'eb';
            })
        );

        foreach ($nonShrinkable->values() as $value) {
            $this->assertFalse($value->shrinkable());
        }

        $shrinkable = Decorate::immutable(
            function(int $value) {
                return [$value];
            },
            Set\Integers::any(),
        );

        foreach ($shrinkable->values() as $value) {
            $this->assertTrue($value->shrinkable());
        }
    }

    public function testShrinkedValuesConserveMutability()
    {
        $mutable = Decorate::mutable(
            function(int $value) {
                $std = new \stdClass;
                $std->prop = $value;

                return $std;
            },
            Set\Integers::any(),
        );

        foreach ($mutable->values() as $value) {
            $dichotomy = $value->shrink();

            $this->assertFalse($dichotomy->a()->isImmutable());
            $this->assertFalse($dichotomy->b()->isImmutable());
        }

        $immutable = Decorate::immutable(
            function(int $value) {
                return [$value];
            },
            Set\Integers::any(),
        );

        foreach ($immutable->values() as $value) {
            $dichotomy = $value->shrink();

            $this->assertTrue($dichotomy->a()->isImmutable());
            $this->assertTrue($dichotomy->b()->isImmutable());
        }
    }

    public function testShrinkedValuesAlwaysRespectTheSetPredicate()
    {
        $set = Decorate::immutable(
            function(int $value) {
                return [$value];
            },
            Set\Integers::any(),
        )->filter(fn($v) => $v[0] % 2 === 0);

        foreach ($set->values() as $value) {
            $dichotomy = $value->shrink();

            $this->assertSame(0, $dichotomy->a()->unwrap()[0] % 2);
            $this->assertSame(0, $dichotomy->b()->unwrap()[0] % 2);
        }
    }
}
