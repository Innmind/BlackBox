<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider\Strings;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Seed,
};

/**
 * @implements Provider<non-empty-string>
 */
final class Chars implements Provider
{
    /**
     * @psalm-mutation-free
     */
    private function __construct()
    {
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function of(): self
    {
        return new self;
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<non-empty-string>
     */
    public function lowercaseLetter(): Set
    {
        return Set::integers()
            ->between(97, 122)
            ->map(\chr(...));
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<non-empty-string>
     */
    public function uppercaseLetter(): Set
    {
        return Set::integers()
            ->between(65, 90)
            ->map(\chr(...));
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<non-empty-string>
     */
    public function number(): Set
    {
        return Set::integers()
            ->between(48, 57)
            ->map(\chr(...));
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<non-empty-string>
     */
    public function ascii(): Set
    {
        return Set::integers()
            ->between(32, 126)
            ->map(\chr(...));
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<non-empty-string>
     */
    public function alphanumerical(): Set
    {
        return Set::either(
            $this->lowercaseLetter(),
            $this->uppercaseLetter(),
            $this->number(),
        );
    }

    /**
     * @psalm-mutation-free
     *
     * @param int<1, max> $size
     *
     * @return Set<non-empty-string>
     */
    public function take(int $size): Set
    {
        return $this->toSet()->take($size);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(non-empty-string): bool $predicate
     *
     * @return Set<non-empty-string>
     */
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(non-empty-string): (V|Seed<V>) $map
     *
     * @return Set<V>
     */
    public function map(callable $map): Set
    {
        return $this->toSet()->map($map);
    }

    /**
     * @psalm-mutation-free
     *
     * @template V
     *
     * @param callable(Seed<non-empty-string>): (Set<V>|Provider<V>) $map
     *
     * @return Set<V>
     */
    public function flatMap(callable $map): Set
    {
        return $this->toSet()->flatMap($map);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function toSet(): Set
    {
        return Set::integers()
            ->between(0, 255)
            ->map(\chr(...));
    }
}
