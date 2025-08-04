<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Collapse,
};

/**
 * @psalm-immutable
 */
final class Given
{
    /** @var Set<list<mixed>> */
    private Set $args;

    /**
     * @param Set<list<mixed>> $args
     */
    private function __construct(Set $args)
    {
        $this->args = $args;
    }

    /**
     * @psalm-pure
     *
     * @param Set<list<mixed>>|Provider<list<mixed>> $args
     */
    public static function of(Set|Provider $args): self
    {
        return new self(Collapse::of($args));
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
     * @return Set<list<mixed>>
     */
    public function set(): Set
    {
        return $this->args;
    }
}
