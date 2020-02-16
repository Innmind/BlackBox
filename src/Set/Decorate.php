<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

/**
 * @template D
 * @implements Set<D>
 */
final class Decorate implements Set
{
    private \Closure $decorate;
    private Set $set;
    private \Closure $predicate;
    private bool $immutable;

    private function __construct(bool $immutable, callable $decorate, Set $set)
    {
        $this->decorate = \Closure::fromCallable($decorate);
        $this->set = $set;
        $this->immutable = $immutable;
        $this->predicate = static fn(): bool => true;
    }

    /**
     * @param callable $decorate It must be a pure function (no randomness, no side effects)
     */
    public static function immutable(callable $decorate, Set $set): self
    {
        return new self(true, $decorate, $set);
    }

    /**
     * @param callable $decorate It must be a pure function (no randomness, no side effects)
     */
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
        $previous = $this->predicate;
        $self = clone $this;
        /** @psalm-suppress MissingClosureParamType */
        $self->predicate = static function($value) use ($previous, $predicate): bool {
            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    public function values(): \Generator
    {
        foreach ($this->set->values() as $value) {
            /** @var mixed */
            $decorated = ($this->decorate)($value->unwrap());

            if (!($this->predicate)($decorated)) {
                continue;
            }

            if ($value->isImmutable() && $this->immutable) {
                yield Value::immutable(
                    $decorated,
                    $this->shrink(false, $value),
                );
            } else {
                // we don't need to re-apply the predicate when we handle mutable
                // data as the underlying data is already validated and the mutable
                // nature is about the enclosing of the data and should not be part
                // of the filtering process
                /** @psalm-suppress MissingClosureReturnType */
                yield Value::mutable(
                    fn() => ($this->decorate)($value->unwrap()),
                    $this->shrink(true, $value),
                );
            }
        }
    }

    private function shrink(bool $mutable, Value $value): ?Dichotomy
    {
        if (!$value->shrinkable()) {
            return null;
        }

        $shrinked = $value->shrink();

        return new Dichotomy(
            $this->shrinkWithStrategy($mutable, $value, $shrinked->a()),
            $this->shrinkWithStrategy($mutable, $value, $shrinked->b()),
        );
    }

    private function shrinkWithStrategy(bool $mutable, Value $value, Value $strategy): callable
    {
        /** @var D */
        $shrinked = ($this->decorate)($strategy->unwrap());

        if (!($this->predicate)($shrinked)) {
            return $this->identity($mutable, $value);
        }

        if ($mutable) {
            /** @psalm-suppress MissingClosureReturnType */
            return fn(): Value => Value::mutable(
                fn() => ($this->decorate)($strategy->unwrap()),
                $this->shrink(true, $strategy),
            );
        }

        return fn(): Value => Value::immutable(
            ($this->decorate)($strategy->unwrap()),
            $this->shrink(false, $strategy),
        );
    }

    private function identity(bool $mutable, Value $value): callable
    {
        if ($mutable) {
            /** @psalm-suppress MissingClosureReturnType */
            return fn(): Value => Value::mutable(
                fn() => ($this->decorate)($value->unwrap()),
            );
        }

        return fn(): Value => Value::immutable(
            ($this->decorate)($value->unwrap()),
        );
    }
}
