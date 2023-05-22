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
    RaiseBy,
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
            ->then(static function($properties) {
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
            ->then(static function($scenario, $initial) {
                $scenario->ensureHeldBy(new Counter($initial));
            });
    }

    public function testParameterizedProperties()
    {
        $this
            ->forAll(Set\Properties::any(
                LowerBoundAtZero::any(),
                UpperBoundAtHundred::any(),
                UpAndDownIsAnIdentityFunction::any(),
                DownAndUpIsAnIdentityFunction::any(),
                DownChangeState::any(),
                UpChangeState::any(),
                RaiseBy::any(),
            ))
            ->then(static function($properties) {
                $properties->ensureHeldBy(new Counter);
            });
    }
}
