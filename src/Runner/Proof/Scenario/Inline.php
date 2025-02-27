<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof\Scenario;

use Innmind\BlackBox\{
    Runner\Assert,
    Runner\Proof\Scenario,
    PHPUnit\Compatibility,
};

final class Inline implements Scenario
{
    /** @var list<mixed> */
    private array $args;
    /** @var \Closure(Assert, ...mixed): void */
    private \Closure $test;

    /**
     * @param list<mixed> $args
     * @param \Closure(Assert, ...mixed): void $test
     */
    private function __construct(
        array $args,
        \Closure $test,
    ) {
        $this->args = $args;
        $this->test = $test;
    }

    #[\Override]
    public function __invoke(Assert $assert): mixed
    {
        ($this->test)($assert, ...$this->args);

        return null;
    }

    /**
     * @internal
     *
     * @param list<mixed> $args
     * @param \Closure(Assert, ...mixed): void $test
     */
    public static function of(
        array $args,
        \Closure $test,
    ): self {
        return new self($args, $test);
    }

    /**
     * @return list<array{string, mixed}>
     */
    public function parameters(): array
    {
        $reflection = new \ReflectionFunction($this->test);
        $testThis = $reflection->getClosureThis();

        if ($testThis instanceof Compatibility) {
            /**
             * @psalm-suppress MixedArrayAccess
             * @var \Closure
             */
            $innerTest = $reflection->getClosureUsedVariables()['test'];
            $parameters = (new \ReflectionFunction($innerTest))->getParameters();
        } else {
            $parameters = $reflection->getParameters();
            \array_shift($parameters); // to remove the Assert parameter
        }

        $parameters = \array_map(
            static fn($parameter) => $parameter->getName(),
            $parameters,
        );
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
