<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof\Scenario;

use Innmind\BlackBox\{
    Runner\Assert,
    Runner\Proof\Scenario,
    Set\Value,
};

final class Failure extends \Exception
{
    /**
     * @param list<array{string, mixed}> $parameters
     */
    private function __construct(
        private Assert\Failure $failure,
        private array $parameters,
    ) {
    }

    /**
     * @internal
     *
     * @param Value<Scenario> $scenario
     */
    public static function of(
        Assert\Failure $failure,
        Value $scenario,
        Assert\Debug $debug,
    ): self {
        return new self(
            $failure,
            [
                ...$scenario->unwrap()->parameters(),
                ...$debug->parameters(),
            ],
        );
    }

    /**
     * @internal
     *
     * @param list<array{string, mixed}> $parameters
     */
    public static function from(
        Assert\Failure $failure,
        array $parameters,
    ): self {
        return new self($failure, $parameters);
    }

    /**
     * @return list<array{string, mixed}>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    public function assertion(): Assert\Failure
    {
        return $this->failure;
    }
}
