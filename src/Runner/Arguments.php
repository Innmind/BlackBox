<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\Set\Value;

final class Arguments
{
    /** @var Value<list<mixed>> */
    private Value $args;
    /** @var list<string> */
    private array $names;

    /**
     * @param Value<list<mixed>> $args
     * @param list<string> $names
     */
    public function __construct(Value $args, array $names)
    {
        $this->args = $args;
        $this->names = $names;
    }

    /**
     * Will call the function with each pair of argument name and value
     *
     * @param callable(string, mixed): void $call
     */
    public function __invoke(callable $call): void
    {
        /** @var mixed $value */
        foreach ($this->args->unwrap() as $i => $value) {
            $call($this->names[$i] ?? 'undefined', $value);
        }
    }
}
