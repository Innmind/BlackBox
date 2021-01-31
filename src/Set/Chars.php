<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
};

/**
 * @implements Set<string>
 */
final class Chars implements Set
{
    /** @var Set<string> */
    private Set $set;

    public function __construct()
    {
        $this->set = self::any();
    }

    /**
     * @return Set<string>
     */
    public static function any(): Set
    {
        return Decorate::immutable(
            static fn(int $ord): string => \chr($ord),
            Integers::between(0, 255),
        );
    }

    public function take(int $size): Set
    {
        return $this->set->take($size);
    }

    public function filter(callable $predicate): Set
    {
        return $this->set->filter($predicate);
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(Random $rand): \Generator
    {
        return $this->set->values($rand);
    }
}
