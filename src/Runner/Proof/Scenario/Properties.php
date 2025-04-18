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

    #[\Override]
    public function __invoke(Assert $assert): mixed
    {
        try {
            $this->properties->ensureHeldBy(
                $assert,
                $this->systemUnderTest,
            );
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
