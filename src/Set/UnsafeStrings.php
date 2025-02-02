<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\{
    Set,
    Random,
    Exception\EmptySet,
};
use Innmind\Json\Json;

/**
 * @implements Set<string>
 */
final class UnsafeStrings implements Set
{
    /** @var positive-int */
    private int $size;
    /** @var \Closure(string): bool */
    private \Closure $predicate;

    /**
     * @psalm-mutation-free
     *
     * @param positive-int $size
     * @param \Closure(string): bool $predicate
     */
    private function __construct(
        int $size,
        \Closure $predicate,
    ) {
        $this->size = $size;
        $this->predicate = $predicate;
    }

    /**
     * @psalm-pure
     */
    public static function any(): self
    {
        return new self(100, static fn(): bool => true);
    }

    /**
     * @psalm-mutation-free
     */
    public function take(int $size): Set
    {
        return new self(
            $size,
            $this->predicate,
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function filter(callable $predicate): Set
    {
        $previous = $this->predicate;

        return new self(
            $this->size,
            static function(string $value) use ($previous, $predicate): bool {
                if (!$previous($value)) {
                    return false;
                }

                return $predicate($value);
            },
        );
    }

    /**
     * @psalm-mutation-free
     */
    public function map(callable $map): Set
    {
        return Decorate::immutable($map, $this);
    }

    public function values(Random $random): \Generator
    {
        $json = \file_get_contents(__DIR__.'/unsafeStrings.json');

        if ($json === false) {
            throw new \RuntimeException('Unable to load unsafe strings dictionary');
        }

        /** @var list<string> */
        $values = Json::decode($json);
        $values = \array_values(\array_filter(
            $values,
            $this->predicate,
        ));

        if (\count($values) === 0) {
            throw new EmptySet;
        }

        $size = \count($values) - 1;
        $iterations = 0;

        while ($iterations < $this->size) {
            $index = $random->between(0, $size);
            $value = $values[$index];

            yield Value::immutable($value, $this->shrink($value));
            ++$iterations;
        }
    }

    /**
     * @return Dichotomy<string>|null
     */
    private function shrink(string $value): ?Dichotomy
    {
        if ($value === '') {
            return null;
        }

        return new Dichotomy(
            $this->removeTrailingCharacter($value),
            $this->removeLeadingCharacter($value),
        );
    }

    /**
     * @return callable(): Value<string>
     */
    private function removeTrailingCharacter(string $value): callable
    {
        $shrinked = \mb_substr($value, 0, -1, 'ASCII');

        if (!($this->predicate)($shrinked)) {
            return $this->identity($value);
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

    /**
     * @return callable(): Value<string>
     */
    private function removeLeadingCharacter(string $value): callable
    {
        $shrinked = \mb_substr($value, 1, null, 'ASCII');

        if (!($this->predicate)($shrinked)) {
            return $this->identity($value);
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

    /**
     * Non shrinkable as it is alreay the minimum value accepted by the predicate
     *
     * @return callable(): Value<string>
     */
    private function identity(string $value): callable
    {
        return static fn(): Value => Value::immutable($value);
    }
}
