<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    PHPUnit\Framework\TestCase,
};

class SequenceTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set\Provider::class,
            Set::sequence(Set::strings()->chars()),
        );
    }

    public function testGenerates100ValuesByDefault()
    {
        $sequences = Set::sequence(Set::strings()->chars())->toSet();

        $this->assertInstanceOf(\Generator::class, $sequences->values(Random::mersenneTwister));
        $this->assertCount(100, \iterator_to_array($sequences->values(Random::mersenneTwister)));

        foreach ($sequences->values(Random::mersenneTwister) as $sequence) {
            $this->assertInstanceOf(Set\Value::class, $sequence);
            $this->assertIsArray($sequence->unwrap());
        }
    }

    public function testGeneratesSequencesOfDifferentSizes()
    {
        $sequences = Set::sequence(Set::strings()->chars())
            ->between(0, 50)
            ->toSet();
        $sizes = [];

        foreach ($sequences->values(Random::mersenneTwister) as $sequence) {
            $sizes[] = \count($sequence->unwrap());
        }

        $this->assertTrue(\count(\array_unique($sizes)) > 1);
    }

    public function testTake()
    {
        $sequences1 = Set::sequence(Set::strings()->chars())->toSet();
        $sequences2 = $sequences1->take(50);

        $this->assertNotSame($sequences1, $sequences2);
        $this->assertInstanceOf(Set::class, $sequences2);
        $this->assertCount(100, \iterator_to_array($sequences1->values(Random::mersenneTwister)));
        $this->assertCount(50, \iterator_to_array($sequences2->values(Random::mersenneTwister)));
    }

    public function testFilter()
    {
        $sequences = Set::sequence(Set::strings()->chars())->toSet();
        $sequences2 = $sequences->filter(static fn($sequence) => \count($sequence) % 2 === 0);

        $this->assertInstanceOf(Set::class, $sequences2);
        $this->assertNotSame($sequences, $sequences2);

        $hasOddSequence = static fn(bool $hasOddSequence, $sequence) => $hasOddSequence || \count($sequence->unwrap()) % 2 === 1;

        $this->assertTrue(
            \array_reduce(
                \iterator_to_array($sequences->values(Random::mersenneTwister)),
                $hasOddSequence,
                false,
            ),
        );
        $this->assertFalse(
            \array_reduce(
                \iterator_to_array($sequences2->values(Random::mersenneTwister)),
                $hasOddSequence,
                false,
            ),
        );
        $this->assertCount(100, \iterator_to_array($sequences->values(Random::mersenneTwister)));
        $this->assertCount(100, \iterator_to_array($sequences2->values(Random::mersenneTwister)));
    }

    public function testFlagStructureAsMutableWhenUnderlyingSetValuesAreMutable()
    {
        $sequences = Set::sequence(
            Set::decorate(
                static fn() => new \stdClass,
                Set::strings()->chars(),
            ),
        )->toSet();

        foreach ($sequences->values(Random::mersenneTwister) as $sequence) {
            $this->assertFalse($sequence->immutable());
            $this->assertSame(\count($sequence->unwrap()), \count($sequence->unwrap()));

            if (\count($sequence->unwrap()) !== 0) {
                $a = $sequence->unwrap();
                $b = $sequence->unwrap();

                $this->assertNotSame(
                    \reset($a),
                    \reset($b),
                    'Objects should not be the same instance between unwraps',
                );
            }
        }
    }

    public function testNonEmptySequenceCanBeShrunk()
    {
        $sequences = Set::sequence(Set::strings()->chars())
            ->between(1, 100)
            ->toSet();

        foreach ($sequences->values(Random::mersenneTwister) as $value) {
            if (\count($value->unwrap()) === 1) {
                // as it can generate sequences of 1 element
                continue;
            }

            $this->assertNotNull($value->shrink());
        }
    }

    public function testEmptySequenceCanNotBeShrunk()
    {
        $sequences = Set::sequence(Set::strings()->chars())
            ->between(0, 1)
            ->toSet();

        foreach ($sequences->values(Random::mersenneTwister) as $value) {
            if (\count($value->unwrap()) === 1) {
                // as it can generate sequences of 1 element
                continue;
            }

            $this->assertNull($value->shrink());
        }
    }

    public function testNonEmptySequenceAreShrunkWithDifferentStrategies()
    {
        $sequences = Set::sequence(Set::strings()->chars())
            ->between(3, 100)
            ->toSet();

        foreach ($sequences->values(Random::mersenneTwister) as $value) {
            if (\count($value->unwrap()) < 6) {
                // when less than the double of the lower limit strategy A will
                // fallback to strategy B
                continue;
            }

            $dichotomy = $value->shrink();
            $initialSize = \count($value->unwrap());
            $this->assertNotSame(
                $dichotomy->a()->unwrap(),
                $dichotomy->b()->unwrap(),
                "Initial sequence size: {$initialSize}",
            );
        }
    }

    public function testShrunkSequencesDoContainsLessThanTheInitialValue()
    {
        $sequences = Set::sequence(Set::strings()->chars())
            ->between(2, 100)
            ->toSet();

        foreach ($sequences->values(Random::mersenneTwister) as $value) {
            if (\count($value->unwrap()) < 4) {
                // otherwise strategy A will return it's identity since 3/2 won't
                // match the predicate of minimum size 2, so strategy will return
                // an identity value
                continue;
            }

            $dichotomy = $value->shrink();

            $this->assertLessThan(\count($value->unwrap()), \count($dichotomy->a()->unwrap()));
            $this->assertLessThan(\count($value->unwrap()), \count($dichotomy->b()->unwrap()));
        }
    }

    public function testShrinkingStrategyAReduceTheSequenceFasterThanStrategyB()
    {
        $sequences = Set::sequence(Set::strings()->chars())
            ->between(3, 100)
            ->toSet();

        foreach ($sequences->values(Random::mersenneTwister) as $value) {
            if (\count($value->unwrap()) < 6) {
                // otherwise strategy A will return it's identity since 5/2 won't
                // match the predicate of minimum size 3, so strategy will return
                // an identity value so it will always be greater than stragey B
                continue;
            }

            $dichotomy = $value->shrink();

            $this->assertLessThan(
                \count($dichotomy->b()->unwrap()),
                \count($dichotomy->a()->unwrap()),
            );
        }
    }

    public function testShrunkValuesConserveMutabilityProperty()
    {
        $sequences = Set::sequence(Set::strings()->chars())
            ->between(1, 100)
            ->toSet();

        foreach ($sequences->values(Random::mersenneTwister) as $value) {
            $dichotomy = $value->shrink();

            if (\is_null($dichotomy)) {
                continue;
            }

            $this->assertTrue($dichotomy->a()->immutable());
            $this->assertTrue($dichotomy->b()->immutable());
        }

        $sequences = Set::sequence(
            Set::decorate(
                static fn() => new \stdClass,
                Set::strings()->chars(),
            ),
        )
            ->between(1, 100)
            ->toSet();

        foreach ($sequences->values(Random::mersenneTwister) as $value) {
            if (\count($value->unwrap()) === 1) {
                // lower bound is not shrinkable
                continue;
            }

            $dichotomy = $value->shrink();

            $this->assertFalse($dichotomy->a()->immutable());
            $this->assertFalse($dichotomy->b()->immutable());
        }
    }

    public function testSequenceIsNeverShrunkBelowTheSpecifiedLowerBound()
    {
        $sequences = Set::sequence(Set::strings()->chars())
            ->between(10, 50)
            ->toSet();

        foreach ($sequences->values(Random::mersenneTwister) as $sequence) {
            while ($shrunk = $sequence->shrink()) {
                $sequence = $shrunk->a();
            }

            $this->assertCount(10, $sequence->unwrap());
        }
    }

    public function testShrunkMutableDataIsRebuiltEverytime()
    {
        $sequences = Set::sequence(
            Set::decorate(
                static function() {
                    $s = new \stdClass;
                    $s->mutated = false;

                    return $s;
                },
                Set::integers(),
            ),
        )
            ->between(1, 20)
            ->toSet();

        foreach ($sequences->values(Random::mersenneTwister) as $value) {
            if (!$value->shrink()) {
                continue;
            }

            $a = $value->shrink()->a()->unwrap();
            $a[0]->mutated = true;
            $this->assertFalse($value->shrink()->a()->unwrap()[0]->mutated);

            $b = $value->shrink()->b()->unwrap();
            $b[0]->mutated = true;
            $this->assertFalse($value->shrink()->b()->unwrap()[0]->mutated);
        }
    }

    public function testStrategyAAlwaysLeadToSmallestValuePossible()
    {
        $sequences = Set::sequence(Set::integers())
            ->between(1, 100)
            ->toSet();

        foreach ($sequences->values(Random::mersenneTwister) as $sequence) {
            while ($shrunk = $sequence->shrink()) {
                $sequence = $shrunk->a();
            }

            $this->assertSame([0], $sequence->unwrap());
        }
    }
}
