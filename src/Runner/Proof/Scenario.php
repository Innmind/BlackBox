<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

use Innmind\BlackBox\Runner\Assert;

final class Scenario
{
    /**
     * @param list<mixed> $args
     * @param \Closure(Assert, ...mixed): void $test
     * @param ?\Closure(): list<string> $nameParameters
     */
    private function __construct(
        private array $args,
        private \Closure $test,
        private ?\Closure $nameParameters,
    ) {
    }

    public function __invoke(Assert $assert): void
    {
        try {
            ($this->test)($assert, ...$this->args);
        } catch (Assert\Failure|Scenario\Failure $e) {
            throw $e;
        } catch (\Throwable $e) {
            $assert->not()->throws(static fn() => throw $e);
        }
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

    /**
     * @return list<array{string, mixed}>
     */
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
