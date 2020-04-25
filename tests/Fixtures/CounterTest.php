<?php
declare(strict_types = 1);

namespace Tests\Innmind\BlackBox\Fixtures;

use Fixtures\Innmind\BlackBox\{
    Counter,
    LowerBoundAtZero,
    UpperBoundAtHundred,
    UpAndDownIsAnIdentityFunction,
    DownAndUpIsAnIdentityFunction,
    DownChangeState,
    UpChangeState,
};
use PHPUnit\Framework\TestCase;
use Innmind\BlackBox\{
    PHPUnit\BlackBox,
    Set,
    Properties,
};

class CounterTest extends TestCase
{
    use BlackBox;

    public function testProperties()
    {
        $this
            ->forAll(Set\Properties::of(
                new LowerBoundAtZero,
                new UpperBoundAtHundred,
                new UpAndDownIsAnIdentityFunction,
                new DownAndUpIsAnIdentityFunction,
                new DownChangeState,
                new UpChangeState,
            ))
            ->then(function($properties) {
                $properties->ensureHeldBy(new Counter);
            });
    }

    public function testPropertiesStartingWithRandomInitialState()
    {
        $this
            ->forAll(
                Set\Properties::of(
                    new LowerBoundAtZero,
                    new UpperBoundAtHundred,
                    new UpAndDownIsAnIdentityFunction,
                    new DownAndUpIsAnIdentityFunction,
                    new DownChangeState,
                    new UpChangeState,
                ),
                Set\Integers::between(0, 100),
            )
            ->then(function($scenario, $initial) {
                $scenario->ensureHeldBy(new Counter($initial));
            });
    }

    /** @group failing-on-purpose */
    public function testWillDisplayStepsToFailure()
    {
        $this
            ->forAll(Set\Properties::of(
                new LowerBoundAtZero,
                new UpperBoundAtHundred,
                new UpAndDownIsAnIdentityFunction,
                new DownAndUpIsAnIdentityFunction,
                new DownChangeState,
                new UpChangeState,
            ))
            ->then(function($properties) {
                $properties->ensureHeldBy(Counter::failOnPurpose());
            });
    }
}
