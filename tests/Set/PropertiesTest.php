<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Properties,
    Set,
    Property,
    Properties as PropertiesModel,
    Random\MtRand,
};

class PropertiesTest extends TestCase
{
    public function testInterface()
    {
        $this->assertInstanceOf(
            Set::class,
            Properties::of(
                $this->createMock(Property::class),
            ),
        );
    }

    public function testGenerate100ScenariiByDefault()
    {
        $properties = Properties::of(
            $this->createMock(Property::class),
        );

        $this->assertCount(100, $properties->values(new MtRand));
    }

    public function testGeneratePropertiesModel()
    {
        $properties = Properties::of(
            $this->createMock(Property::class),
        );

        foreach ($properties->values(new MtRand) as $scenario) {
            $this->assertInstanceOf(PropertiesModel::class, $scenario->unwrap());
        }
    }

    public function testValuesAreConsideredImmutable()
    {
        $properties = Properties::of(
            $this->createMock(Property::class),
        );

        foreach ($properties->values(new MtRand) as $scenario) {
            $this->assertTrue($scenario->isImmutable());
        }
    }

    public function testScenariiAreOfDifferentSizes()
    {
        $properties = Properties::of(
            $this->createMock(Property::class),
        );
        $sizes = [];

        foreach ($properties->values(new MtRand) as $scenario) {
            $sizes[] = \count($scenario->unwrap()->properties());
        }

        $this->assertGreaterThan(25, \count(\array_unique($sizes)));
    }

    public function testTake()
    {
        $properties = Properties::of(
            $this->createMock(Property::class),
        );
        $properties2 = $properties->take(50);

        $this->assertInstanceOf(Set::class, $properties2);
        $this->assertNotSame($properties, $properties2);
        $this->assertCount(100, $properties->values(new MtRand));
        $this->assertCount(50, $properties2->values(new MtRand));
    }

    public function testFilter()
    {
        $properties = Properties::of(
            $this->createMock(Property::class),
        );
        $properties2 = $properties->filter(fn($scenario) => \count($scenario->properties()) > 50);

        $this->assertInstanceOf(Set::class, $properties2);
        $this->assertNotSame($properties, $properties2);

        $hasUnder50Properties = fn(bool $hasUnder50Properties, $scenario) => $hasUnder50Properties || \count($scenario->properties()) < 50;

        $this->assertTrue(
            \array_reduce(
                $this->unwrap($properties->values(new MtRand)),
                $hasUnder50Properties,
                false,
            ),
        );
        $this->assertFalse(
            \array_reduce(
                $this->unwrap($properties2->values(new MtRand)),
                $hasUnder50Properties,
                false,
            ),
        );
    }
}
