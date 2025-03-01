<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Implementation,
};

/**
 * @implements Provider<int>
 */
final class Integers implements Provider
{
    /**
     * @psalm-mutation-free
     *
     * @param pure-Closure(Implementation<int>): Set<int> $wrap
     */
    private function __construct(
        private \Closure $wrap,
        private ?int $min,
        private ?int $max,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     *
     * @param pure-Closure(Implementation<int>): Set<int> $wrap
     */
    public static function of(\Closure $wrap): self
    {
        return new self($wrap, null, null);
    }

    /**
     * @psalm-mutation-free
     */
    public function between(int $min, int $max): self
    {
        return new self($this->wrap, $min, $max);
    }

    /**
     * @psalm-mutation-free
     */
    public function above(int $min): self
    {
        return new self($this->wrap, $min, null);
    }

    /**
     * @psalm-mutation-free
     */
    public function below(int $max): self
    {
        return new self($this->wrap, null, $max);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function toSet(): Set
    {
        return ($this->wrap)(Set\Integers::implementation(
            $this->min,
            $this->max,
        ));
    }
}
