<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\Set;

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
     * @param Set<list<mixed>> $args
     */
    public static function of(Set $args): self
    {
        return new self($args);
    }

    /**
     * @param callable(...mixed): bool $filter
     */
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
