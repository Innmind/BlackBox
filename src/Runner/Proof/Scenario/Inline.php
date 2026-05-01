<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof\Scenario;

use Innmind\BlackBox\Runner\{
    Assert,
    Proof\Scenario,
};

final class Inline implements Scenario
{
    /** @var list<mixed> */
    private array $args;
    /** @var \Closure(Assert, ...mixed): void */
    private \Closure $test;
    /** @var ?\Closure(): list<string> */
    private ?\Closure $nameParameters;

    /**
     * @param list<mixed> $args
     * @param \Closure(Assert, ...mixed): void $test
     * @param ?\Closure(): list<string> $nameParameters
     */
    private function __construct(
        array $args,
        \Closure $test,
        ?\Closure $nameParameters,
    ) {
        $this->args = $args;
        $this->test = $test;
        $this->nameParameters = $nameParameters;
    }

    #[\Override]
    public function __invoke(Assert $assert): void
    {
        ($this->test)($assert, ...$this->args);
    }

    /**
     * @internal
     *
     * @param list<mixed> $args
     * @param \Closure(Assert, ...mixed): void $test
     * @param ?\Closure(): list<string> $nameParameters
     */
    public static function of(
        array $args,
        \Closure $test,
        ?\Closure $nameParameters = null,
    ): self {
        return new self($args, $test, $nameParameters);
    }

    #[\Override]
    public function parameters(): array
    {
        if (\is_null($this->nameParameters)) {
            $reflection = new \ReflectionFunction($this->test);
            $parameters = $reflection->getParameters();
            \array_shift($parameters); // to remove the Assert parameter

            $parameters = \array_map(
                static fn($parameter) => $parameter->getName(),
                $parameters,
            );
        } else {
            $parameters = ($this->nameParameters)();
        }

        $args = [];

        /** @var mixed $arg */
        foreach ($this->args as $index => $arg) {
            $args[] = [
                $parameters[$index] ?? 'undefined',
                $arg,
            ];
        }

        return $args;
    }
}
