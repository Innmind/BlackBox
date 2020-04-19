<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Sequence,
    Set,
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

        $this->assertInstanceOf(\Generator::class, $sequences->values());
        $this->assertCount(100, \iterator_to_array($sequences->values()));

        foreach ($sequences->values() as $sequence) {
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

        foreach ($sequences->values() as $sequence) {
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
        $this->assertCount(100, \iterator_to_array($sequences1->values()));
        $this->assertCount(50, \iterator_to_array($sequences2->values()));
    }

    public function testFilter()
    {
        $sequences = Sequence::of(Set\Chars::any());

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Sequence set can\'t be filtered, underlying set must be filtered beforehand');

        $sequences->filter(static function($sequence): bool {
            return $sequence->size() % 2 === 0;
        });
    }

    public function testFlagStructureAsMutableWhenUnderlyingSetValuesAreMutable()
    {
        $sequences = Sequence::of(
            Set\Decorate::mutable(
                fn() => new \stdClass,
                Set\Chars::any(),
            ),
        );

        foreach ($sequences->values() as $sequence) {
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

        foreach ($sequences->values() as $value) {
            $this->assertTrue($value->shrinkable());
        }
    }

    public function testEmptySequenceCanNotBeShrunk()
    {
        $sequences = Sequence::of(Set\Chars::any(), Set\Integers::below(1));

        foreach ($sequences->values() as $value) {
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

        foreach ($sequences->values() as $value) {
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

        foreach ($sequences->values() as $value) {
            $dichotomy = $value->shrink();

            $this->assertLessThan(\count($value->unwrap()), \count($dichotomy->a()->unwrap()));
            $this->assertLessThan(\count($value->unwrap()), \count($dichotomy->b()->unwrap()));
        }
    }

    public function testShrinkingStrategyAReduceTheSequenceFasterThanStrategyB()
    {
        $sequences = Sequence::of(Set\Chars::any(), Set\Integers::between(3, 100));

        foreach ($sequences->values() as $value) {
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

        foreach ($sequences->values() as $value) {
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

        foreach ($sequences->values() as $value) {
            $dichotomy = $value->shrink();

            $this->assertFalse($dichotomy->a()->isImmutable());
            $this->assertFalse($dichotomy->b()->isImmutable());
        }
    }
}
