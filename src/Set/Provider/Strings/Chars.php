<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Provider\Strings;

use Innmind\BlackBox\{
    Set,
    Set\Provider,
    Set\Seed,
    Exception\EmptySet,
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
    #[\NoDiscard]
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
    #[\NoDiscard]
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
    #[\NoDiscard]
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
    #[\NoDiscard]
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
    #[\NoDiscard]
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
    #[\NoDiscard]
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
    #[\NoDiscard]
    public function filter(callable $predicate): Set
    {
        return $this->toSet()->filter($predicate);
    }

    /**
     * @psalm-mutation-free
     *
     * @param callable(non-empty-string): bool $predicate
     *
     * @return Set<non-empty-string>
     */
    #[\NoDiscard]
    public function exclude(callable $predicate): Set
    {
        return $this->toSet()->exclude($predicate);
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
    #[\NoDiscard]
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
    #[\NoDiscard]
    public function flatMap(callable $map): Set
    {
        return $this->toSet()->flatMap($map);
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<non-empty-string>
     */
    #[\NoDiscard]
    public function randomize(): Set
    {
        return $this->toSet()->randomize();
    }

    /**
     * @psalm-mutation-free
     *
     * @return Set<?non-empty-string>
     */
    #[\NoDiscard]
    public function nullable(): Set
    {
        return $this->toSet()->nullable();
    }

    /**
     * @throws EmptySet When no value can be generated
     *
     * @return iterable<non-empty-string>
     */
    #[\NoDiscard]
    public function enumerate(): iterable
    {
        return $this->toSet()->enumerate();
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    #[\NoDiscard]
    public function toSet(): Set
    {
        return Set::integers()
            ->between(0, 255)
            ->map(\chr(...));
    }
}
