<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Composite;

use Innmind\BlackBox\Set;

final class Matrix implements \Iterator
{
    private $combinations;

    public function __construct(Combination ...$combinations)
    {
        $this->combinations = $combinations;
    }

    public function dot(Set $set): self
    {
        $combinations = $set->reduce(
            [],
            function(array $combinations, $value): array {
                return \array_merge(
                    $combinations,
                    $this->add($value)->combinations
                );
            }
        );

        return new self(...$combinations);
    }

    public function current(): array
    {
        return \current($this->combinations)->toArray();
    }

    public function key(): int
    {
        return \key($this->combinations);
    }

    public function next(): void
    {
        \next($this->combinations);
    }

    public function rewind(): void
    {
        \reset($this->combinations);
    }

    public function valid(): bool
    {
        return !\is_null(\key($this->combinations));
    }

    private function add($value): self
    {
        $combinations = [];

        foreach ($this->combinations as $combination) {
            $combinations[] = $combination->add($value);
        }

        return new self(...$combinations);
    }
}
