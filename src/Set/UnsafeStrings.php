<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;
use Innmind\Json\Json;

/**
 * @implements Set<string>
 */
final class UnsafeStrings implements Set
{
    private int $size;
    private \Closure $predicate;

    public function __construct()
    {
        $this->size = 100;
        $this->predicate = static fn(): bool => true;
    }

    public static function any(): self
    {
        return new self;
    }

    public function take(int $size): Set
    {
        $self = clone $this;
        $self->size = $size;

        return $self;
    }

    public function filter(callable $predicate): Set
    {
        $previous = $this->predicate;
        $self = clone $this;
        /** @psalm-suppress MissingClosureParamType */
        $self->predicate = static function($value) use ($previous, $predicate): bool {
            if (!$previous($value)) {
                return false;
            }

            return $predicate($value);
        };

        return $self;
    }

    /**
     * @psalm-suppress MixedReturnTypeCoercion
     */
    public function values(): \Generator
    {
        /** @var list<string> */
        $values = Json::decode(\file_get_contents(__DIR__.'/unsafeStrings.json'));
        \shuffle($values);

        $values = \array_filter($values, $this->predicate);
        $values = \array_slice($values, 0, $this->size);

        foreach ($values as $value) {
            yield Value::immutable($value, $this->shrink($value));
        }
    }

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

    private function removeTrailingCharacter(string $value): callable
    {
        $shrinked = \mb_substr($value, 0, -1, 'ASCII');

        if (!($this->predicate)($shrinked)) {
            return $this->identity($value);
        }

        return fn(): Value => Value::immutable($shrinked, $this->shrink($shrinked));
    }

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
     */
    private function identity(string $value): callable
    {
        return static fn(): Value => Value::immutable($value);
    }
}
