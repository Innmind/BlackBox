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
    private Assert\Failure $failure;
    /** @var Value<Scenario> */
    private Value $scenario;
    private Assert\Debug $debug;

    /**
     * @param Value<Scenario> $scenario
     */
    private function __construct(
        Assert\Failure $failure,
        Value $scenario,
        Assert\Debug $debug,
    ) {
        $this->failure = $failure;
        $this->scenario = $scenario;
        $this->debug = $debug;
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
        return new self($failure, $scenario, $debug);
    }

    /**
     * @return Value<Scenario>
     */
    public function scenario(): Value
    {
        return $this->scenario;
    }

    /**
     * @return list<array{string, mixed}>
     */
    public function parameters(): array
    {
        return [
            ...$this->scenario->unwrap()->parameters(),
            ...$this->debug->parameters(),
        ];
    }

    public function assertion(): Assert\Failure
    {
        return $this->failure;
    }
}
