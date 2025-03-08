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
            static function(string $value) use ($previous, $predicate): bool {
                if (!$previous($value)) {
                    return false;
                }

                return $predicate($value);
            },
            $this->size,
        );
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function map(callable $map): Implementation
    {
        return Map::implementation($map, $this, true);
    }

    /**
     * @psalm-mutation-free
     */
    #[\Override]
    public function flatMap(callable $map, callable $extract): Implementation
    {
        return FlatMap::implementation(
            static fn(string $input) => $extract($map($input)),
            $this,
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
            $value = $values[$index];

            yield Value::immutable($value)
                ->shrinkWith($this->shrink($value));
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

        return Dichotomy::of(
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

        return fn(): Value => Value::immutable($shrinked)
            ->shrinkWith($this->shrink($shrinked));
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

        return fn(): Value => Value::immutable($shrinked)
            ->shrinkWith($this->shrink($shrinked));
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
