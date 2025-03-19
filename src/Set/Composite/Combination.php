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

    /**
     * @internal
     */
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
            static fn(bool $immutable, Value $value): bool => $immutable && $value->immutable(),
            true,
        );
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
     * @param int<0, max> $n
     */
    public function has(int $n): bool
    {
        return \array_key_exists($n, $this->values);
    }

    /**
     * @param 0|positive-int $n
     */
    public function aShrinkNth(int $n): ?self
    {
        $shrunk = $this->values;
        $nShrunk = $this->values[$n]->shrink();

        if (\is_null($nShrunk)) {
            return null;
        }

        $shrunk[$n] = $nShrunk->a();

        return new self(\array_values($shrunk));
    }

    /**
     * @param 0|positive-int $n
     */
    public function bShrinkNth(int $n): ?self
    {
        $shrunk = $this->values;
        $nShrunk = $this->values[$n]->shrink();

        if (\is_null($nShrunk)) {
            return null;
        }

        $shrunk[$n] = $nShrunk->b();

        return new self(\array_values($shrunk));
    }

    private function unwrap(): array
    {
        return \array_map(
            static fn(Value $value): mixed => $value->unwrap(),
            $this->values,
        );
    }
}
