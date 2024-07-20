<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\Value;

/**
 * @internal
 */
final class Combination
{
    /** @var non-empty-list<Value<mixed>> */
    private array $values;

    /**
     * @param non-empty-list<Value<mixed>> $values
     */
    private function __construct(array $values)
    {
        $this->values = $values;
    }

    public static function startWith(Value $right): self
    {
        return new self([$right]);
    }

    public function add(Value $left): self
    {
        $self = clone $this;
        \array_unshift($self->values, $left);

        return $self;
    }

    public function immutable(): bool
    {
        return \array_reduce(
            $this->values,
            static fn(bool $immutable, Value $value): bool => $immutable && $value->isImmutable(),
            true,
        );
    }

    /**
     * @return list<Value>
     */
    public function values(): array
    {
        return $this->values;
    }

    /**
     * @template T
     *
     * @param callable(mixed...): T $aggregate
     *
     * @return T
     */
    public function detonate(callable $aggregate): mixed
    {
        return $aggregate(...$this->unwrap());
    }

    /**
     * @param 0|positive-int $n
     */
    public function aShrinkNth(int $n): self
    {
        $shrunk = [];

        foreach ($this->values as $i => $value) {
            if ($i === $n) {
                $value = $value->shrink()->a();
            }

            $shrunk[] = $value;
        }

        return new self($shrunk);
    }

    /**
     * @param 0|positive-int $n
     */
    public function bShrinkNth(int $n): self
    {
        $shrunk = [];

        foreach ($this->values as $i => $value) {
            if ($i === $n) {
                $value = $value->shrink()->b();
            }

            $shrunk[] = $value;
        }

        return new self($shrunk);
    }

    public function shrinkable(): bool
    {
        return \array_reduce(
            $this->values,
            static fn(bool $shrinkable, Value $value): bool => $shrinkable || $value->shrinkable(),
            false,
        );
    }

    private function unwrap(): array
    {
        return \array_map(
            static fn(Value $value): mixed => $value->unwrap(),
            $this->values,
        );
    }
}
