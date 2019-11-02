<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Set,
    Set\Composite,
};

final class Scenario
{
    private $set;

    public function __construct(Set $first , Set ...$sets)
    {
        if (\count($sets) === 0) {
            $set = new Set\Decorate(
                static function($value): array {
                    return [$value];
                },
                $first
            );
        } else {
            $set = new Set\Composite(
                function(...$args): array {
                    return $args;
                },
                $first,
                ...$sets
            );
        }

        $this->set = $set->take(100);
    }

    public function take(int $size): self
    {
        $self = clone $this;
        $self->set = $this->set->take($size);

        return $self;
    }

    /**
     * @param callable(): bool $predicate
     */
    public function filter(callable $predicate): self
    {
        $self = clone $this;
        $self->set = $this->set->filter($predicate);

        return $self;
    }

    public function then(callable $test): void
    {
        $values = $this->set->values();

        foreach ($values as $values) {
            $test(...$values);
        }
    }
}
