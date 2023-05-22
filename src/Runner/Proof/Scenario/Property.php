<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof\Scenario;

use Innmind\BlackBox\{
    Runner\Assert,
    Runner\Proof\Scenario,
    Property as Concrete,
};

final class Property implements Scenario
{
    private Concrete $property;
    private object $systemUnderTest;

    private function __construct(
        Concrete $property,
        object $systemUnderTest,
    ) {
        $this->property = $property;
        $this->systemUnderTest = $systemUnderTest;
    }

    public function __invoke(Assert $assert): mixed
    {
        $this->property->ensureHeldBy($assert, $this->systemUnderTest);

        return null;
    }

    public static function of(
        Concrete $property,
        object $systemUnderTest,
    ): self {
        return new self($property, $systemUnderTest);
    }
}
