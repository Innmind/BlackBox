<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

use Innmind\BlackBox\Exception\FirstNotHeld;

final class Hold
{
    /** @var callable(callable(): void, callable(string): void, TestResult, ...mixed): void */
    private $assertion;
    /** @var list<string> */
    private $trace;

    /**
     * @param callable(callable(): void, callable(string): void, TestResult, ...mixed): void $assertion
     */
    public function __construct(callable $assertion)
    {
        $this->assertion = $assertion;
        /** @var list<array{class?: string, type: string, file?: string, function: string, line?: int}> */
        $trace = \debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS);
        /** @var list<array{class?: string, type: string, file: string, function: string, line: int}> */
        $trace = \array_filter(
            $trace,
            static fn(array $frame): bool => \array_key_exists('file', $frame) && \array_key_exists('line', $frame),
        );
        $trace = \array_filter(
            $trace,
            static fn(array $frame): bool => \strpos($frame['file'], 'innmind/black-box/src') === false,
        );
        $this->trace = \array_values(\array_map(
            static fn(array $frame): string => "{$frame['file']}:{$frame['line']}",
            $trace,
        ));
    }

    /**
     * @param callable(): void $held To count the number of assertions
     * @param callable(string, list<string>): void $fail
     * @param mixed $args
     */
    public function __invoke(
        callable $held,
        callable $fail,
        TestResult $result,
        ...$args
    ): void {
        ($this->assertion)(
            $held,
            function(string $reason) use ($fail): void {
                $fail($reason, $this->trace);
            },
            $result,
            ...$args,
        );
    }

    public static function all(self $hold, self ...$rest): self
    {
        \array_unshift($rest, $hold);

        /** @psalm-suppress MissingClosureParamType */
        return new self(static function(
            callable $held,
            callable $fail,
            TestResult $result,
            ...$args
        ) use ($rest): void {
            foreach ($rest as $hold) {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                $hold($held, $fail, $result, ...$args);
            }
        });
    }

    public static function either(self $one, self $other): self
    {
        /** @psalm-suppress MissingClosureParamType */
        return new self(static function(
            callable $held,
            callable $fail,
            TestResult $result,
            ...$args
        ) use ($one, $other) {
            try {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                $one(
                    $held,
                    static function(): void {
                        throw new FirstNotHeld;
                    },
                    $result,
                    ...$args,
                );
            } catch (FirstNotHeld $e) {
                /** @psalm-suppress MixedArgumentTypeCoercion */
                $other($held, $fail, $result, ...$args);
            }
        });
    }

    public static function exceptionThrown(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => $result->thrown(),
            'No exception has been thrown',
        );
    }

    public static function noExceptionThrown(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !$result->thrown(),
            'An exception has been thrown',
        );
    }

    public static function exceptionCode(int $code): self
    {
        /** @psalm-suppress MixedMethodCall For getCode() */
        return self::satisfies(
            static fn(TestResult $result): bool => $result->value()->getCode() === $code,
            "Exception code is not $code",
        );
    }

    public static function exceptionMessage(string $message): self
    {
        /** @psalm-suppress MixedMethodCall For getMessage() */
        return self::satisfies(
            static fn(TestResult $result): bool => $result->value()->getMessage() === $message,
            "Exception message is not $message",
        );
    }

    public static function instanceOf(string $class): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => $result->value() instanceof $class,
            "Value is not an instanceof $class",
        );
    }

    public static function isArray(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => \is_array($result->value()),
            'Value is not an array',
        );
    }

    public static function isBool(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => \is_bool($result->value()),
            'Value is not a boolean',
        );
    }

    public static function isFloat(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => \is_float($result->value()),
            'Value is not a float',
        );
    }

    public static function isInt(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => \is_int($result->value()),
            'Value is not an integer',
        );
    }

    public static function isNumeric(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => \is_numeric($result->value()),
            'Value is not a numeric',
        );
    }

    public static function isObject(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => \is_object($result->value()),
            'Value is not an object',
        );
    }

    public static function isResource(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => \is_resource($result->value()),
            'Value is not a resource',
        );
    }

    public static function isString(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => \is_string($result->value()),
            'Value is not a string',
        );
    }

    public static function isScalar(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => \is_scalar($result->value()),
            'Value is not a scalar',
        );
    }

    public static function isCallable(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => \is_callable($result->value()),
            'Value is not a callable',
        );
    }

    public static function isIterable(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => \is_iterable($result->value()),
            'Value is not an interable',
        );
    }

    public static function notInstanceOf(string $class): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !($result->value() instanceof $class),
            "Value is an instanceof $class",
        );
    }

    public static function isNotArray(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !\is_array($result->value()),
            'Value is an array',
        );
    }

    public static function isNotBool(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !\is_bool($result->value()),
            'Value is a boolean',
        );
    }

    public static function isNotFloat(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !\is_float($result->value()),
            'Value is a float',
        );
    }

    public static function isNotInt(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !\is_int($result->value()),
            'Value is an integer',
        );
    }

    public static function isNotNumeric(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !\is_numeric($result->value()),
            'Value is a numeric',
        );
    }

    public static function isNotObject(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !\is_object($result->value()),
            'Value is an object',
        );
    }

    public static function isNotResource(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !\is_resource($result->value()),
            'Value is a resource',
        );
    }

    public static function isNotString(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !\is_string($result->value()),
            'Value is a string',
        );
    }

    public static function isNotScalar(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !\is_scalar($result->value()),
            'Value is a scalar',
        );
    }

    public static function isNotCallable(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !\is_callable($result->value()),
            'Value is a callable',
        );
    }

    public static function isNotIterable(): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => !\is_iterable($result->value()),
            'Value is an interable',
        );
    }

    public static function arrayHasKey(string $key): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => \array_key_exists($key, $result->value()),
            "Array doesn't contain the key $key",
        );
    }

    public static function arrayNotHasKey(string $key): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => !\array_key_exists($key, $result->value()),
            "Array contains the key $key",
        );
    }

    /**
     * @param mixed $value
     */
    public static function inArray($value): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => \in_array($value, $result->value(), true),
            'Expected value not in array',
        );
    }

    /**
     * @param mixed $value
     */
    public static function notInArray($value): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => !\in_array($value, $result->value(), true),
            'Expected value found in array',
        );
    }

    public static function count(int $count): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => \count($result->value()) === $count,
            "Value doesn't have $count element(s)",
        );
    }

    public static function notCount(int $count): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => \count($result->value()) !== $count,
            "Value have $count element(s)",
        );
    }

    /**
     * @param int|float $value
     */
    public static function greaterThan($value): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => $result->value() > $value,
            "Value less than or equal to $value",
        );
    }

    /**
     * @param int|float $value
     */
    public static function greaterThanOrEqual($value): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => $result->value() >= $value,
            "Value less than $value",
        );
    }

    /**
     * @param int|float $value
     */
    public static function lessThan($value): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => $result->value() < $value,
            "Value greater than or equal to $value",
        );
    }

    /**
     * @param int|float $value
     */
    public static function lessThanOrEqual($value): self
    {
        return self::satisfies(
            static fn(TestResult $result): bool => $result->value() <= $value,
            "Value greater than $value",
        );
    }

    public static function stringStartsWith(string $start): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => $start === '' || \strpos($result->value(), $start) === 0,
            "Value doesn't start with $start",
        );
    }

    public static function stringDoesntStartWith(string $start): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => $start !== '' && \strpos($result->value(), $start) !== 0,
            "Value starts with $start",
        );
    }

    public static function stringContains(string $string): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => $string === '' || \strpos($result->value(), $string) !== false,
            "Value doesn't contain $string",
        );
    }

    public static function stringDoesntContain(string $string): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => $string !== '' && \strpos($result->value(), $string) === false,
            "Value contains $string",
        );
    }

    public static function stringEndsWith(string $end): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => $end === '' || \substr($result->value(), -\strlen($end)) === $end,
            "Value doesn't end with $end",
        );
    }

    public static function stringDoesntEndWith(string $end): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => $end !== '' && \substr($result->value(), -\strlen($end)) !== $end,
            "Value ends with $end",
        );
    }

    /**
     * @param callable(TestResult, ...mixed): bool $condition
     */
    public static function satisfies(callable $condition, string $message = null): self
    {
        /** @psalm-suppress MissingClosureParamType */
        return new self(static function(
            callable $held,
            callable $fail,
            TestResult $result,
            ...$args
        ) use ($condition, $message): void {
            if (!$condition($result, ...$args)) {
                $fail($message ?? 'Value not held by the condition');
            } else {
                $held();
            }
        });
    }

    /**
     * @param callable(TestResult, ...mixed): bool $condition
     */
    public static function doesntSatisfy(callable $condition, string $message = null): self
    {
        /** @psalm-suppress MissingClosureParamType */
        return self::satisfies(
            static fn(TestResult $result, ...$args): bool => !$condition($result, ...$args),
            $message ?? 'Value held by the condition',
        );
    }

    /**
     * @param callable(...mixed): mixed $find
     */
    public static function same(callable $find, string $message = null): self
    {
        /** @psalm-suppress MissingClosureParamType */
        return self::satisfies(
            static fn(TestResult $result, ...$args): bool => $result->value() === $find(...$args),
            $message ?? 'Value different than the expected one',
        );
    }

    /**
     * @param callable(...mixed): mixed $find
     */
    public static function notSame(callable $find, string $message = null): self
    {
        /** @psalm-suppress MissingClosureParamType */
        return self::doesntSatisfy(
            static fn(TestResult $result, ...$args): bool => $result->value() === $find(...$args),
            $message ?? 'Value same as the expected one',
        );
    }

    /**
     * @param mixed $value
     */
    public static function is($value, string $message = null): self
    {
        /** @psalm-suppress MissingClosureReturnType */
        return self::same(static fn() => $value, $message);
    }

    /**
     * @param mixed $value
     */
    public static function notIs($value, string $message = null): self
    {
        /** @psalm-suppress MissingClosureReturnType */
        return self::notSame(static fn() => $value, $message);
    }

    public static function matches(string $pattern): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => \preg_match($pattern, $result->value()) === 1,
            "Value doesn't match the pattern $pattern",
        );
    }

    public static function doesntMatch(string $pattern): self
    {
        /** @psalm-suppress MixedArgument */
        return self::satisfies(
            static fn(TestResult $result): bool => \preg_match($pattern, $result->value()) !== 1,
            "Value matches the pattern $pattern",
        );
    }
}
