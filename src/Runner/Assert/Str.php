<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

use Innmind\BlackBox\Runner\{
    Stats,
    Assert\Failure\Property,
};

final class Str
{
    private Stats $stats;
    private string $value;

    private function __construct(Stats $stats, string $value)
    {
        $this->stats = $stats;
        $this->value = $value;
    }

    /**
     * @internal
     */
    public static function of(Stats $stats, string $value): self
    {
        return new self($stats, $value);
    }

    #[\NoDiscard]
    public function not(): Str\Not
    {
        return Str\Not::of($this->stats, $this->value);
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function empty(?string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (\strlen($this->value) !== 0) {
            throw Failure::of(Property::of(
                $this->value,
                $message ?? 'Failed to assert a string is empty',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $needle
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function contains(string $needle, ?string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (!\str_contains($this->value, $needle)) {
            throw Failure::of(Property::of(
                $this->value,
                $message ?? \sprintf(
                    'Failed to assert a string contains "%s"',
                    $needle,
                ),
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $regex
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function matches(string $regex, ?string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (\preg_match($regex, $this->value) !== 1) {
            throw Failure::of(Property::of(
                $this->value,
                $message ?? \sprintf(
                    'Failed to assert a string matches the pattern "%s"',
                    $regex,
                ),
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $needle
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function startsWith(string $needle, ?string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (!\str_starts_with($this->value, $needle)) {
            throw Failure::of(Property::of(
                $this->value,
                $message ?? \sprintf(
                    'Failed to assert a string starts with "%s"',
                    $needle,
                ),
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $needle
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function endsWith(string $needle, ?string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (!\str_ends_with($this->value, $needle)) {
            throw Failure::of(Property::of(
                $this->value,
                $message ?? \sprintf(
                    'Failed to assert a string ends with "%s"',
                    $needle,
                ),
            ));
        }

        return $this;
    }
}
