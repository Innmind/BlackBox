<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
};

/**
 * @psalm-immutable
 */
final class Given
{
    /**
     * @param Set<list<mixed>> $args
     */
    private function __construct(private Set $args)
    {
    }

    /**
     * @psalm-pure
     *
     * @param Set<list<mixed>>|Provider<list<mixed>> $args
     */
    public static function of(Set|Provider $args): self
    {
        return new self($args->toSet());
    }

    /**
     * @param callable(...mixed): bool $filter
     */
    #[\NoDiscard]
    public function filter(callable $filter): self
    {
        return new self($this->args->filter(static fn($args) => $filter(...$args)));
    }

    /**
     * @param callable(...mixed): bool $filter
     */
    #[\NoDiscard]
    public function exclude(callable $filter): self
    {
        return $this->filter(static fn(...$args) => !$filter(...$args));
    }

    /**
     * @return Set<list<mixed>>
     */
    public function set(): Set
    {
        return $this->args;
    }
}
