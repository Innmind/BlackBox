<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set\Property,
    Set\Integers,
    Random,
};
use PHPUnit\Framework\TestCase;
use Fixtures\Innmind\BlackBox\{
    UpChangeState,
    RaiseBy,
};

class PropertyTest extends TestCase
{
    public function testNoRequiredInputAlwaysReturnTheSamePropertyObjects()
    {
        $values = Property::of(UpChangeState::class)->values(Random::default);
        $previous = $values->current()->unwrap();
        $values->next();

        while ($values->valid()) {
            $this->assertSame($previous, $values->current()->unwrap());
            $previous = $values->current()->unwrap();
            $values->next();
        }
    }

    public function testPropertyWithOnlyOneArgumentIsRebuiltEveryTimeWithADifferentInput()
    {
        $values = Property::of(RaiseBy::class, Integers::above(0))->values(Random::default);
        $previous = $values->current()->unwrap();
        $values->next();

        while ($values->valid()) {
            $this->assertNotSame(
                $previous->name(),
                $values->current()->unwrap()->name(),
            );
            $previous = $values->current()->unwrap();
            $values->next();
        }
    }

    public function testPropertyWithMultipleInputsArgumentIsRebuiltEveryTimeWithDifferentInputs()
    {
        $values = Property::of(
            RaiseBy::class,
            Integers::above(0),
            Integers::above(0), // unused argument, only here to test behaviour with the composite
        )->values(Random::default);
        $previous = $values->current()->unwrap();
        $values->next();

        while ($values->valid()) {
            $this->assertNotSame(
                $previous,
                $values->current()->unwrap(),
            );
            $previous = $values->current()->unwrap();
            $values->next();
        }
    }
}
