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
 * @internal
 * @implements Implementation<string>
 */
final class UnsafeStrings implements Implementation
{
    /**
     * @psalm-mutation-free
     *
     * @param \Closure(string): bool $predicate
     * @param int<1, max> $size
     */
    private function __construct(
        private \Closure $predicate,
        private int $size,
    ) {
    }

    /**
     * @internal
     * @psalm-pure
     */
    public static function implementation(): self
    {
        return new self(static fn(): bool => true, 100);
    }

    /**
     * @deprecated Use Set::strings()->unsafe() instead
     * @psalm-pure
     *
     * @return Set<string>
     */
    public static function any(): Set
    {
        return Set::strings()->unsafe();
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function take(int $size): self
    {
        return new self(
            $this->predicate,
            $size,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function filter(callable $predicate): self
    {
        $previous = $this->predicate;

        return new self(
            static fn(string $value) => $previous($value) && $predicate($value),
            $this->size,
        );
    }

    #[\Override]
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
            $value = Value::immutable($values[$index])
                ->predicatedOn($this->predicate);

            yield $value->shrinkWith(self::shrink($value));
            ++$iterations;
        }
    }

    /**
     * @param Value<string> $value
     *
     * @return Dichotomy<string>|null
     */
    private static function shrink(Value $value): ?Dichotomy
    {
        if ($value->unwrap() === '') {
            return null;
        }

        return new Dichotomy(
            self::removeTrailingCharacter($value),
            self::removeLeadingCharacter($value),
        );
    }

    /**
     * @param Value<string> $value
     *
     * @return callable(): Value<string>
     */
    private static function removeTrailingCharacter(Value $value): callable
    {
        $shrunk = $value->map(static fn($string) => \mb_substr(
            $string,
            0,
            -1,
            'ASCII',
        ));

        if (!$shrunk->acceptable()) {
            return static fn() => $value->withoutShrinking();
        }

        return static fn(): Value => $shrunk->shrinkWith(self::shrink($shrunk));
    }

    /**
     * @param Value<string> $value
     *
     * @return callable(): Value<string>
     */
    private static function removeLeadingCharacter(Value $value): callable
    {
        $shrunk = $value->map(static fn($string) => \mb_substr(
            $string,
            1,
            null,
            'ASCII',
        ));

        if (!$shrunk->acceptable()) {
            return static fn() => $value->withoutShrinking();
        }

        return static fn(): Value => $shrunk->shrinkWith(self::shrink($shrunk));
    }
}
