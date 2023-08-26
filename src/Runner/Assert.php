<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\Runner\Assert\{
    Failure,
    Failure\Truth,
    Failure\Property,
    Failure\Comparison,
    Expected,
};

final class Assert
{
    private Stats $stats;

    private function __construct(Stats $stats)
    {
        $this->stats = $stats;
    }

    /**
     * @internal
     */
    public static function of(Stats $stats): self
    {
        return new self($stats);
    }

    /**
     * @template R
     *
     * @param callable(self): R $assert
     *
     * @return R
     */
    public function matches(callable $assert): mixed
    {
        return $assert($this);
    }

    public function not(): Assert\Not
    {
        return Assert\Not::of($this->stats);
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function fail(string $message): void
    {
        throw Failure::of(Truth::of($message));
    }

    /**
     * @param callable(): bool $assertion
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function that(
        callable $assertion,
        string $message = null,
    ): self {
        $this->stats->incrementAssertions();

        if (!$assertion()) {
            throw Failure::of(Truth::of($message ?? 'Failed to verify that an assertion is true'));
        }

        return $this;
    }

    public function expected(mixed $value): Expected
    {
        return Expected::of($this->stats, $value);
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function same(mixed $a, mixed $b, string $message = null): self
    {
        $this->expected($a)->same($b);

        return $this;
    }

    /**
     * @param class-string<\Throwable> $class
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function throws(
        callable $attempt,
        string $class = null,
        string $message = null,
    ): self {
        $this->stats->incrementAssertions();

        try {
            $attempt();

            throw Failure::of(Property::of(
                $attempt,
                $message ?? 'Failed asserting that a callable throws an exception'),
            );
        } catch (Failure $e) {
            throw $e;
        } catch (\Throwable $e) {
            if (\is_string($class) && !($e instanceof $class)) {
                throw Failure::of(Property::of(
                    $attempt,
                    $message ?? \sprintf(
                        'Failed asserting that a callable throws the exception %s',
                        $class,
                    ),
                ));
            }
        }

        return $this;
    }

    /**
     * @param 0|positive-int $expected
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function count(
        int $expected,
        \Countable|array $collection,
        string $message = null,
    ): self {
        $this->stats->incrementAssertions();

        $count = \count($collection);

        if ($count !== $expected) {
            throw Failure::of(Comparison::of(
                $expected,
                $count,
                $message ?? \sprintf(
                    'Failed to assert that a collection contains %s element(s)',
                    $expected,
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
    public function true(mixed $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($actual !== true) {
            throw Failure::of(Property::of(
                $actual,
                $message ?? 'Failed to assert a variable is true',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function false(mixed $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($actual !== false) {
            throw Failure::of(Property::of(
                $actual,
                $message ?? 'Failed to assert a variable is false',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function bool(mixed $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if (!\is_bool($actual)) {
            throw Failure::of(Property::of(
                $actual,
                $message ?? 'Failed to assert a variable is a boolean',
            ));
        }

        return $this;
    }

    /**
     * @param non-empty-string $message
     *
     * @throws Failure
     */
    public function null(mixed $actual, string $message = null): self
    {
        $this->stats->incrementAssertions();

        if ($actual !== null) {
            throw Failure::of(Property::of(
                $actual,
                $message ?? 'Failed to assert a variable is null',
            ));
        }

        return $this;
    }

    /**
     * @throws Failure
     */
    public function resource(mixed $actual): Assert\Resource
    {
        $this->stats->incrementAssertions();

        if (!\is_resource($actual)) {
            throw Failure::of(Property::of(
                $actual,
                'Failed to assert a variable is a resource',
            ));
        }

        return Assert\Resource::of($this->stats, $actual);
    }

    /**
     * @throws Failure
     */
    public function object(mixed $actual): Assert\Objet
    {
        $this->stats->incrementAssertions();

        if (!\is_object($actual)) {
            throw Failure::of(Property::of(
                $actual,
                'Failed to assert a variable is an object',
            ));
        }

        return Assert\Objet::of($this->stats, $actual);
    }

    /**
     * @throws Failure
     */
    public function number(mixed $actual): Assert\Number
    {
        $this->stats->incrementAssertions();

        if (!\is_int($actual) && !\is_float($actual)) {
            throw Failure::of(Property::of(
                $actual,
                'Failed to assert a variable is a number',
            ));
        }

        return Assert\Number::of($this->stats, $actual);
    }

    /**
     * @throws Failure
     */
    public function string(mixed $actual): Assert\Str
    {
        $this->stats->incrementAssertions();

        if (!\is_string($actual)) {
            throw Failure::of(Property::of(
                $actual,
                'Failed to assert a variable is a string',
            ));
        }

        return Assert\Str::of($this->stats, $actual);
    }

    /**
     * @throws Failure
     */
    public function array(mixed $actual): Assert\Arr
    {
        $this->stats->incrementAssertions();

        if (!\is_array($actual)) {
            throw Failure::of(Property::of(
                $actual,
                'Failed to assert a variable is an array',
            ));
        }

        return Assert\Arr::of($this->stats, $actual);
    }

    /**
     * @param callable(): void $action
     */
    public function time(callable $action): Assert\Time
    {
        return Assert\Time::of($this->stats, $action);
    }

    /**
     * @param callable(): void $action
     */
    public function memory(callable $action): Assert\Memory
    {
        return Assert\Memory::of($this->stats, $action);
    }
}
