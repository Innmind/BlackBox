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
final class Strings implements Set
{
    private int $maxLength;
    private int $minLength;
    private int $size;
    private \Closure $predicate;

    public function __construct(int $boundA = null, int $boundB = null)
    {
        // this trick is here because historically only the maxLength was configurable
        // with the first argument
        $boundA ??= 128;
        $boundB ??= 0;

        $this->maxLength = \max($boundA, $boundB);
        $this->minLength = \min($boundA, $boundB);
        $this->size = 100;
        $this->predicate = fn(string $value): bool => mb_strlen($value) >= $this->minLength && mb_strlen($value) <= $this->maxLength;
    }

    public static function any(int $maxLength = null): self
    {
        if (\is_int($maxLength)) {
            \trigger_error('Use Strings::atMost() instead', \E_USER_DEPRECATED);
        }

        return new self($maxLength);
    }

    public static function between(int $minLength, int $maxLength): self
    {
        return new self($minLength, $maxLength);
    }

    public static function atMost(int $maxLength): self
    {
        return new self(0, $maxLength);
    }

    public static function atLeast(int $minLength): self
    {
        return new self($minLength, $minLength + 128);
    }

    /**
     * @see https://github.com/icomefromthenet/ReverseRegex For the supported expressions
     */
    public static function matching(string $expression): Regex
    {
        return Regex::for($expression);
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
        $self->predicate = static function(string $value) use ($previous, $predicate): bool {
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
    public function values(Random $rand): \Generator
    {
        $iterations = 0;

        while ($iterations < $this->size) {
            $value = '';
            $maxLength = $rand($this->minLength + 1, $this->maxLength);

            for ($i = 0; $i < $maxLength; $i++) {
                $value .= \chr($rand(33, 126));
            }

            if (!($this->predicate)($value)) {
                continue ;
            }

            yield Value::immutable(
                $value,
                $this->shrink($value),
            );
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
        /** @var string */
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
        /** @var string */
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
