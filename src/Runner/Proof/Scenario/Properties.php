<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof\Scenario;

use Innmind\BlackBox\{
    Runner\Assert,
    Runner\Proof\Scenario,
    Properties as Concrete,
    Property,
};

final class Properties implements Scenario
{
    private Concrete $properties;
    private object $systemUnderTest;

    private function __construct(
        Concrete $properties,
        object $systemUnderTest,
    ) {
        $this->properties = $properties;
        $this->systemUnderTest = $systemUnderTest;
    }

    public function __invoke(Assert $assert): mixed
    {
        $this->properties->ensureHeldBy(
            $assert,
            $this->systemUnderTest,
        );

        return null;
    }

    /**
     * @internal
     */
    public static function of(
        Concrete $properties,
        object $systemUnderTest,
    ): self {
        return new self($properties, $systemUnderTest);
    }

    /**
     * @return list<Property>
     */
    public function properties(): array
    {
        return $this->properties->properties();
    }

    public function systemUnderTest(): object
    {
        return $this->systemUnderTest;
    }
}
