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
            function() use ($value): Value { // remove trailing character
                $shrinked = \mb_substr($value, 0, -1, 'ASCII');

                return Value::immutable($shrinked, $this->shrink($shrinked));
            },
            function() use ($value): Value { // remove leading character
                $shrinked = \mb_substr($value, 1, null, 'ASCII');

                return Value::immutable($shrinked, $this->shrink($shrinked));
            },
        );
    }
}
