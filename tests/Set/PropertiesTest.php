<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Properties,
    Set,
    Set\Collapse,
    Properties as PropertiesModel,
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
            Properties::any(
                Set\Elements::of(new LowerBoundAtZero),
            ),
        );
    }

    public function testGenerate100ScenariiByDefault()
    {
        $properties = Properties::any(
            Set\Elements::of(new LowerBoundAtZero),
        );

        $this->assertCount(100, \iterator_to_array(Collapse::of($properties)->values(Random::mersenneTwister)));
    }

    public function testGeneratePropertiesModel()
    {
        $properties = Properties::any(
            Set\Elements::of(new LowerBoundAtZero),
        );

        foreach (Collapse::of($properties)->values(Random::mersenneTwister) as $scenario) {
            $this->assertInstanceOf(PropertiesModel::class, $scenario->unwrap());
        }
    }

    public function testValuesAreConsideredImmutable()
    {
        $properties = Properties::any(
            Set\Elements::of(new LowerBoundAtZero),
        );

        foreach (Collapse::of($properties)->values(Random::mersenneTwister) as $scenario) {
            $this->assertTrue($scenario->immutable());
        }
    }

    public function testScenariiAreOfDifferentSizes()
    {
        $properties = Properties::any(
            Set\Elements::of(new LowerBoundAtZero),
        );
        $sizes = [];

        foreach (Collapse::of($properties)->values(Random::mersenneTwister) as $scenario) {
            $sizes[] = \count($scenario->unwrap()->properties());
        }

        $this->assertGreaterThan(25, \count(\array_unique($sizes)));
    }

    public function testTake()
    {
        $properties = Properties::any(
            Set\Elements::of(new LowerBoundAtZero),
        );
        $properties2 = $properties->take(50);

        $this->assertInstanceOf(Set::class, $properties2);
        $this->assertNotSame($properties, $properties2);
        $this->assertCount(100, \iterator_to_array(Collapse::of($properties)->values(Random::mersenneTwister)));
        $this->assertCount(50, \iterator_to_array(Collapse::of($properties2)->values(Random::mersenneTwister)));
    }

    public function testFilter()
    {
        $properties = Properties::any(
            Set\Elements::of(new LowerBoundAtZero),
        );
        $properties2 = $properties->filter(static fn($scenario) => \count($scenario->properties()) > 50);

        $this->assertInstanceOf(Set::class, $properties2);
        $this->assertNotSame($properties, $properties2);

        $hasUnder50Properties = static fn(bool $hasUnder50Properties, $scenario) => $hasUnder50Properties || \count($scenario->properties()) < 50;

        $this->assertTrue(
            \array_reduce(
                $this->unwrap(Collapse::of($properties)->values(Random::mersenneTwister)),
                $hasUnder50Properties,
                false,
            ),
        );
        $this->assertFalse(
            \array_reduce(
                $this->unwrap(Collapse::of($properties2)->values(Random::mersenneTwister)),
                $hasUnder50Properties,
                false,
            ),
        );
    }

    public function testMaxNumberOfPropertiesGeneratedAtOnce()
    {
        $properties = Properties::any(
            Set\Elements::of(new LowerBoundAtZero),
        )->atMost(50);
        $sizes = [];

        foreach (Collapse::of($properties)->values(Random::mersenneTwister) as $scenario) {
            $sizes[] = \count($scenario->unwrap()->properties());
        }

        $this->assertLessThanOrEqual(50, \max($sizes));
    }
}
