<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Properties,
    Random,
    PHPUnit\BlackBox,
};
use Fixtures\Innmind\BlackBox\LowerBoundAtZero;

class PropertiesTest extends TestCase
{
    use BlackBox;

    public function testInterface()
    {
        $this->assertInstanceOf(
            Set\Provider::class,
            Set::properties(
                Set::of(new LowerBoundAtZero),
            ),
        );
    }

    public function testGenerate100ScenariiByDefault()
    {
        $properties = Set::properties(
            Set::of(new LowerBoundAtZero),
        );

        $this->assertCount(
            100,
            \iterator_to_array(
                $properties
                    ->toSet()
                    ->take(100)
                    ->values(Random::mersenneTwister),
            ),
        );
    }

    public function testGeneratePropertiesModel()
    {
        $properties = Set::properties(
            Set::of(new LowerBoundAtZero),
        );

        foreach ($properties->toSet()->take(100)->values(Random::mersenneTwister) as $scenario) {
            $this->assertInstanceOf(Properties::class, $scenario->unwrap());
        }
    }

    public function testScenariiAreOfDifferentSizes()
    {
        $properties = Set::properties(
            Set::of(new LowerBoundAtZero),
        );
        $sizes = [];

        foreach ($properties->toSet()->take(100)->values(Random::mersenneTwister) as $scenario) {
            $sizes[] = \count($scenario->unwrap()->properties());
        }

        $this->assertGreaterThan(25, \count(\array_unique($sizes)));
    }

    public function testTake()
    {
        $properties = Set::properties(
            Set::of(new LowerBoundAtZero),
        )->take(100);
        $properties2 = $properties->take(50);

        $this->assertInstanceOf(Set::class, $properties2);
        $this->assertNotSame($properties, $properties2);
        $this->assertCount(100, \iterator_to_array($properties->toSet()->values(Random::mersenneTwister)));
        $this->assertCount(50, \iterator_to_array($properties2->toSet()->values(Random::mersenneTwister)));
    }

    public function testFilter()
    {
        $properties = Set::properties(
            Set::of(new LowerBoundAtZero),
        );
        $properties2 = $properties->filter(static fn($scenario) => \count($scenario->properties()) > 50);

        $this->assertInstanceOf(Set::class, $properties2);
        $this->assertNotSame($properties, $properties2);

        $hasUnder50Properties = static fn(bool $hasUnder50Properties, $scenario) => $hasUnder50Properties || \count($scenario->properties()) < 50;

        $this->assertTrue(
            \array_reduce(
                $this->unwrap($properties->toSet()->take(100)->values(Random::mersenneTwister)),
                $hasUnder50Properties,
                false,
            ),
        );
        $this->assertFalse(
            \array_reduce(
                $this->unwrap($properties2->toSet()->take(100)->values(Random::mersenneTwister)),
                $hasUnder50Properties,
                false,
            ),
        );
    }

    public function testMaxNumberOfPropertiesGeneratedAtOnce()
    {
        $properties = Set::properties(
            Set::of(new LowerBoundAtZero),
        )->atMost(50);
        $sizes = [];

        foreach ($properties->toSet()->take(100)->values(Random::mersenneTwister) as $scenario) {
            $sizes[] = \count($scenario->unwrap()->properties());
        }

        $this->assertLessThanOrEqual(50, \max($sizes));
    }
}
