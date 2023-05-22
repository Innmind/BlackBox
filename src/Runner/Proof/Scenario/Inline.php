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

    public function __invoke(Assert $assert): mixed
    {
        ($this->test)($assert, ...$this->args);

        return null;
    }

    /**
     * @param list<mixed> $args
     * @param \Closure(Assert, ...mixed): void $test
     */
    public static function of(
        array $args,
        \Closure $test,
    ): self {
        return new self($args, $test);
    }
}
