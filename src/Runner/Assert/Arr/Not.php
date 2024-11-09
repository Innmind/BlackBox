<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert\Arr;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure,
    Assert\Failure\Property,
    Assert\Failure\Comparison,
};

final class Not
{
    private Stats $stats;
    private array $value;

    private function __construct(Stats $stats, array $value)
    {
        $this->stats = $stats;
        $this->value = $value;
    }

    /**
     * @internal
     */
    public static function of(Stats $stats, array $value): self
    {
        return new self($stats, $value);
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function hasKey(int|string $key, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (\array_key_exists($key, $this->value)) {
            /** @psalm-suppress ArgumentTypeCoercion It doesn't understand the message is never empty */
            throw Failure::of(Property::of(
                $this->value,
                $message ?? \sprintf(
                    'Failed to assert an array does not have the key "%s"',
                    $key,
                ),
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function contains(mixed $value, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (\in_array($value, $this->value, true)) {
            throw Failure::of(Comparison::of(
                $value,
                $this->value,
                $message ?? 'Failed to assert an array does not contain a value',
            ));
        }

        return $this;
    }
}
