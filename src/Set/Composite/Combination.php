<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set\Value;

final class Combination
{
    /** @var list<Value> */
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
        /** @var list<Value> */
        $strategyA = [];
        /** @var list<Value> */
        $strategyB = [];
        $foundOne = false;

        foreach ($this->values as $value) {
            if (!$foundOne && $value->shrinkable()) {
                $strategyA[] = $value->shrink()->a();
                $strategyB[] = $value->shrink()->b();
                $foundOne = true;
            } else {
                $strategyA[] = $value;
                $strategyB[] = $value;
            }
        }

        return [
            'a' => self::of(...\array_reverse($strategyA)),
            'b' => self::of(...\array_reverse($strategyB)),
        ];
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
