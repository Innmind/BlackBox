<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Decorate implements Set
{
    private \Closure $decorate;
    private Set $set;
    private bool $immutable;

    private function __construct(bool $immutable, callable $decorate, Set $set)
    {
        $this->decorate = \Closure::fromCallable($decorate);
        $this->set = $set;
        $this->immutable = $immutable;
    }

    public static function immutable(callable $decorate, Set $set): self
    {
        return new self(true, $decorate, $set);
    }

    public static function mutable(callable $decorate, Set $set): self
    {
        return new self(false, $decorate, $set);
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->set = $this->set->take($size);

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $self = clone $this;
        $self->set = $this->set->filter($predicate);

        return $self;
    }

    public function values(): \Generator
    {
        foreach ($this->set->values() as $value) {
            if ($value->isImmutable() && $this->immutable) {
                yield Value::immutable(($this->decorate)($value->unwrap()));
            } else {
                /** @psalm-suppress MissingClosureReturnType */
                yield Value::mutable(fn() => ($this->decorate)($value->unwrap()));
            }
        }
    }
}
