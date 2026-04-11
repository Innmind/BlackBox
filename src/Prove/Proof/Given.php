<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Prove\Proof;

use Innmind\BlackBox\Runner\{
    Proof,
    Proof\Name,
    Assert,
    Given as Given_,
};

/**
 * @psalm-immutable
 */
final class Given
{
    private function __construct(
        private Name $name,
        private Given_ $given,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function of(Name $name, Given_ $given): self
    {
        return new self($name, $given);
    }

    /**
     * @param callable(...mixed): bool $filter
     */
    public function filter(callable $filter): self
    {
        return new self(
            $this->name,
            $this->given->filter($filter),
        );
    }

    /**
     * @param callable(...mixed): bool $filter
     */
    public function exclude(callable $filter): self
    {
        return new self(
            $this->name,
            $this->given->exclude($filter),
        );
    }

    /**
     * @param callable(Assert, ...mixed): void $test
     */
    public function test(callable $test): Proof\Inline
    {
        return Proof\Inline::of(
            $this->name,
            $this->given,
            \Closure::fromCallable($test),
        );
    }
}
