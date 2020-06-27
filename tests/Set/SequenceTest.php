<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Sequence,
    Set,
    Random\MtRand,
};
use PHPUnit\Framework\TestCase;

class SequenceTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            Sequence::of(Set\Chars::any())
        );
    }

    public function testGenerates100ValuesByDefault()
    {
        $sequences = Sequence::of(Set\Chars::any());

        $this->assertInstanceOf(\Generator::class, $sequences->values(new MtRand));
        $this->assertCount(100, \iterator_to_array($sequences->values(new MtRand)));

        foreach ($sequences->values(new MtRand) as $sequence) {
            $this->assertInstanceOf(Set\Value::class, $sequence);
            $this->assertIsArray($sequence->unwrap());
        }
    }

    public function testGeneratesSequencesOfDifferentSizes()
    {
        $sequences = Sequence::of(
            Set\Chars::any(),
            Set\Integers::between(0, 50)
        );
        $sizes = [];

        foreach ($sequences->values(new MtRand) as $sequence) {
            $sizes[] = \count($sequence->unwrap());
        }

        $this->assertTrue(\count(\array_unique($sizes)) > 1);
    }

    public function testTake()
    {
        $sequences1 = Sequence::of(Set\Chars::any());
        $sequences2 = $sequences1->take(50);

        $this->assertNotSame($sequences1, $sequences2);
        $this->assertInstanceOf(Sequence::class, $sequences2);
        $this->assertCount(100, \iterator_to_array($sequences1->values(new MtRand)));
        $this->assertCount(50, \iterator_to_array($sequences2->values(new MtRand)));
    }

    public function testFilter()
    {
        $sequences = Sequence::of(Set\Chars::any());
        $sequences2 = $sequences->filter(fn($sequence) => \count($sequence) % 2 === 0);

        $this->assertInstanceOf(Sequence::class, $sequences2);
        $this->assertNotSame($sequences, $sequences2);

        $hasOddSequence = fn(bool $hasOddSequence, $sequence) => $hasOddSequence || \count($sequence->unwrap()) % 2 === 1;

        $this->assertTrue(
            \array_reduce(
                \iterator_to_array($sequences->values(new MtRand)),
                $hasOddSequence,
                false,
            ),
        );
        $this->assertFalse(
            \array_reduce(
                \iterator_to_array($sequences2->values(new MtRand)),
                $hasOddSequence,
                false,
            ),
        );
        $this->assertCount(100, \iterator_to_array($sequences->values(new MtRand)));
        $this->assertCount(100, \iterator_to_array($sequences2->values(new MtRand)));
    }

    public function testFlagStructureAsMutableWhenUnderlyingSetValuesAreMutable()
    {
        $sequences = Sequence::of(
            Set\Decorate::mutable(
                fn() => new \stdClass,
                Set\Chars::any(),
            ),
        );

        foreach ($sequences->values(new MtRand) as $sequence) {
            $this->assertFalse($sequence->isImmutable());
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
        $sequences = Sequence::of(Set\Chars::any(), Set\Integers::between(1, 100));

        foreach ($sequences->values(new MtRand) as $value) {
            if (\count($value->unwrap()) === 1) {
                // as it can generate sequences of 1 element
                continue;
            }

            $this->assertTrue($value->shrinkable());
        }
    }

    public function testEmptySequenceCanNotBeShrunk()
    {
        $sequences = Sequence::of(Set\Chars::any(), Set\Integers::between(0, 1));

        foreach ($sequences->values(new MtRand) as $value) {
            if (\count($value->unwrap()) === 1) {
                // as it can generate sequences of 1 element
                continue;
            }

            $this->assertFalse($value->shrinkable());
        }
    }

    public function testNonEmptySequenceAreShrunkWithDifferentStrategies()
    {
        $sequences = Sequence::of(Set\Chars::any(), Set\Integers::between(3, 100));

        foreach ($sequences->values(new MtRand) as $value) {
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
        $sequences = Sequence::of(Set\Chars::any(), Set\Integers::between(2, 100));

        foreach ($sequences->values(new MtRand) as $value) {
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
        $sequences = Sequence::of(Set\Chars::any(), Set\Integers::between(3, 100));

        foreach ($sequences->values(new MtRand) as $value) {
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
        $sequences = Sequence::of(Set\Chars::any(), Set\Integers::between(1, 100));

        foreach ($sequences->values(new MtRand) as $value) {
            $dichotomy = $value->shrink();

            $this->assertTrue($dichotomy->a()->isImmutable());
            $this->assertTrue($dichotomy->b()->isImmutable());
        }

        $sequences = Sequence::of(
            Set\Decorate::mutable(
                fn() => new \stdClass,
                Set\Chars::any(),
            ),
            Set\Integers::between(1, 100),
        );

        foreach ($sequences->values(new MtRand) as $value) {
            if (\count($value->unwrap()) === 1) {
                // lower bound is not shrinkable
                continue;
            }

            $dichotomy = $value->shrink();

            $this->assertFalse($dichotomy->a()->isImmutable());
            $this->assertFalse($dichotomy->b()->isImmutable());
        }
    }

    public function testSequenceIsNeverShrunkBelowTheSpecifiedLowerBound()
    {
        $sequences = Sequence::of(Set\Chars::any(), Set\Integers::between(10, 50));

        foreach ($sequences->values(new MtRand) as $sequence) {
            while ($sequence->shrinkable()) {
                $sequence = $sequence->shrink()->a();
            }

            $this->assertCount(10, $sequence->unwrap());
        }
    }

    public function testShrunkMutableDataIsRebuiltEverytime()
    {
        $sequences = Sequence::of(
            Set\Decorate::mutable(
                function() {
                    $s = new \stdClass;
                    $s->mutated = false;

                    return $s;
                },
                Set\Integers::any(),
            ),
            Set\Integers::between(1, 20),
        );

        foreach ($sequences->values(new MtRand) as $value) {
            if (!$value->shrinkable()) {
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
        $sequences = Sequence::of(
            Set\Integers::any(),
            Set\Integers::between(1, 100),
        );

        foreach ($sequences->values(new MtRand) as $sequence) {
            while ($sequence->shrinkable()) {
                $sequence = $sequence->shrink()->a();
            }

            $this->assertSame([0], $sequence->unwrap());
        }
    }
}
