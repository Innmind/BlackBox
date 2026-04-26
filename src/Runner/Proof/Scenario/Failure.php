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
    /** @var list<array{string, mixed}> */
    private array $scenario;
    /** @var list<array{string, mixed}> */
    private array $debug;

    /**
     * @param list<array{string, mixed}> $scenario
     * @param list<array{string, mixed}> $debug
     */
    private function __construct(
        Assert\Failure $failure,
        array $scenario,
        array $debug,
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
        return new self(
            $failure,
            $scenario->unwrap()->parameters(),
            $debug->parameters(),
        );
    }

    /**
     * @internal
     *
     * @param list<array{string, mixed}> $scenario
     * @param list<array{string, mixed}> $debug
     */
    public static function from(
        Assert\Failure $failure,
        array $scenario,
        array $debug,
    ): self {
        return new self($failure, $scenario, $debug);
    }

    /**
     * @return list<array{string, mixed}>
     */
    public function parameters(): array
    {
        return [
            ...$this->scenario,
            ...$this->debug,
        ];
    }

    /**
     * @return list<array{string, mixed}>
     */
    public function scenario(): array
    {
        return $this->scenario;
    }

    /**
     * @return list<array{string, mixed}>
     */
    public function debug(): array
    {
        return $this->debug;
    }

    public function assertion(): Assert\Failure
    {
        return $this->failure;
    }
}
