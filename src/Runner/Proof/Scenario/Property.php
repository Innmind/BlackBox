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
        if (!$this->property->applicableTo($this->systemUnderTest)) {
            $assert->fail('The property is not applicable to the system under test.');
        }

        try {
            $this->property->ensureHeldBy($assert, $this->systemUnderTest);
        } catch (Assert\Failure $e) {
            throw $e;
        } catch (\Throwable $e) {
            $assert->not()->throws(static fn() => throw $e);
        }

        return null;
    }

    /**
     * @internal
     */
    public static function of(
        Concrete $property,
        object $systemUnderTest,
    ): self {
        return new self($property, $systemUnderTest);
    }

    public function property(): Concrete
    {
        return $this->property;
    }

    public function systemUnderTest(): object
    {
        return $this->systemUnderTest;
    }
}
