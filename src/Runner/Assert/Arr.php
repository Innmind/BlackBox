<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure\Property,
};

final class Arr
{
    private Stats $stats;
    private array $value;

    private function __construct(Stats $stats, array $value)
    {
        $this->stats = $stats;
        $this->value = $value;
    }

    public static function of(Stats $stats, array $value): self
    {
        return new self($stats, $value);
    }

    public function not(): Arr\Not
    {
        return Arr\Not::of($this->stats, $this->value);
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function hasKey(int|string $key, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (!\array_key_exists($key, $this->value)) {
            /** @psalm-suppress ArgumentTypeCoercion It doesn't understand the message is never empty */
            throw Failure::of(Property::of(
                $this->value,
                $message ?? \sprintf(
                    'Failed to assert an array has the key "%s"',
                    $key,
                ),
            ));
        }

        return $this;
    }
}
