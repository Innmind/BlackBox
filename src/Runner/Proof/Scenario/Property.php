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
    /** @var \Closure(): object */
    private \Closure $systemUnderTest;

    /**
     * @param \Closure(): object $systemUnderTest
     */
    private function __construct(
        Concrete $property,
        \Closure $systemUnderTest,
    ) {
        $this->property = $property;
        $this->systemUnderTest = $systemUnderTest;
    }

    #[\Override]
    public function __invoke(Assert $assert): mixed
    {
        $sut = ($this->systemUnderTest)();
        $assert->debug('systemUnderTest', $sut);

        if (!$this->property->applicableTo($sut)) {
            $assert->fail('The property is not applicable to the system under test.');
        }

        try {
            $this->property->ensureHeldBy($assert, $sut);
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
        Concrete $property,
        \Closure $systemUnderTest,
    ): self {
        return new self($property, $systemUnderTest);
    }

    public function property(): Concrete
    {
        return $this->property;
    }
}
