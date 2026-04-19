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
    /** @var \Closure(): object */
    private \Closure $systemUnderTest;

    /**
     * @param \Closure(): object $systemUnderTest
     */
    private function __construct(
        Concrete $properties,
        \Closure $systemUnderTest,
    ) {
        $this->properties = $properties;
        $this->systemUnderTest = $systemUnderTest;
    }

    #[\Override]
    public function __invoke(Assert $assert): mixed
    {
        $sut = ($this->systemUnderTest)();
        $assert->debug('systemUnderTest', $sut);

        try {
            $this->properties->ensureHeldBy($assert, $sut);
        } catch (Assert\Failure $e) {
            throw $e;
        } catch (\Throwable $e) {
            $assert->not()->throws(static fn() => throw $e);
        }

        return null;
    }

    /**
     * @internal
     *
     * @param \Closure(): object $systemUnderTest
     */
    public static function of(
        Concrete $properties,
        \Closure $systemUnderTest,
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
}
