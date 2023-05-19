<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\Value;

/**
 * @internal
 */
final class Combination
{
    /** @var non-empty-list<Value> */
    private array $values;

    public function __construct(Value $right)
    {
        $this->values = [$right];
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

    public function unwrap(): array
    {
        /** @psalm-suppress MissingClosureReturnType */
        return \array_map(
            static fn(Value $value) => $value->unwrap(),
            $this->values,
        );
    }

    public function shrinkable(): bool
    {
        return \array_reduce(
            $this->values,
            static fn(bool $shrinkable, Value $value): bool => $shrinkable || $value->shrinkable(),
            false,
        );
    }

    /**
     * @return array{a: self, b: self}
     */
    public function shrink(): array
    {
        return ['a' => $this->shrinkFirst(), 'b' => $this->shrinkSecond()];
    }

    /**
     * @param 'a'|'b' $strategy
     */
    private function shrinkFirst(string $strategy = 'a'): self
    {
        $values = [];
        $foundOne = false;

        foreach ($this->values as $value) {
            if (!$foundOne && $value->shrinkable()) {
                /** @var Value */
                $values[] = $value->shrink()->{$strategy}();
                $foundOne = true;
            } else {
                $values[] = $value;
            }
        }

        return self::of(...\array_reverse($values));
    }

    /**
     * Will try to shrink the second value that can be shrunk
     *
     * It will fallback to shrinking the first one with the "b" strategy if
     * there is only one shrinkable value
     */
    private function shrinkSecond(): self
    {
        $values = [];
        $found = 0;

        foreach ($this->values as $value) {
            if ($value->shrinkable()) {
                $found++;
            }

            if ($found === 2 && $value->shrinkable()) {
                $values[] = $value->shrink()->a();
            } else {
                $values[] = $value;
            }
        }

        if ($found >= 2) {
            return self::of(...\array_reverse($values));
        }

        return $this->shrinkFirst('b');
    }

    private static function of(Value $value, Value ...$values): self
    {
        return \array_reduce(
            $values,
            static fn(self $combination, Value $value): self => $combination->add($value),
            new self($value),
        );
    }
}
