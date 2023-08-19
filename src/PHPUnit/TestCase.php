<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Runner\Assert,
    Runner\Proof\Scenario,
};

abstract class TestCase
{
    private static Assert $assert;
    private ?string $expectedException = null;
    private null|int|string $expectedExceptionCode = null;
    private ?string $expectedExceptionMessage = null;

    final public function __construct(Assert $assert)
    {
        self::$assert = $assert;
    }

    protected function setUp(): void
    {
    }

    protected function tearDown(): void
    {
    }

    final public function executeTest(string $method): void
    {
        $this->setUp();

        try {
            $this->$method();
            $this->tearDown();
        } catch (Assert\Failure|Scenario\Failure $e) {
            throw $e;
        } catch (\Throwable $e) {
            if (!$this->anExceptionIsExpected()) {
                throw $e;
            }

            if ($this->expectedException) {
                self::$assert
                    ->object($e)
                    ->instance($this->expectedException);
            }

            if ($this->expectedExceptionCode) {
                self::$assert->same(
                    $this->expectedExceptionCode,
                    $e->getCode(),
                );
            }

            if ($this->expectedExceptionMessage) {
                self::$assert->same(
                    $this->expectedExceptionMessage,
                    $e->getMessage(),
                );
            }
        }
    }

    final public static function assertSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert
            ->expected($expected)
            ->same($actual, $message);
    }

    final public static function assertNotSame(mixed $expected, mixed $actual, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert
            ->expected($expected)
            ->not()
            ->same($actual, $message);
    }

    final public static function assertInstanceOf(string $expected, mixed $actual, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert
            ->object($actual)
            ->instance($expected, $message);
    }

    final public static function assertCount(int $expectedCount, \Countable|iterable $haystack, string $message = ''): void
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @psalm-suppress PossiblyInvalidArgument
         */
        self::$assert->count($expectedCount, $haystack, $message);
    }

    final public static function assertTrue(mixed $condition, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert->true($condition, $message);
    }

    final public static function assertFalse(mixed $condition, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert->false($condition, $message);
    }

    final public static function assertNotFalse(mixed $condition, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert->not()->false($condition, $message);
    }

    final public static function assertGreaterThanOrEqual(mixed $expected, mixed $actual, string $message = ''): void
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @psalm-suppress MixedArgument
         */
        self::$assert
            ->number($actual)
            ->greaterThanOrEqual($expected, $message);
    }

    final public static function assertGreaterThan(mixed $expected, mixed $actual, string $message = ''): void
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @psalm-suppress MixedArgument
         */
        self::$assert
            ->number($actual)
            ->greaterThan($expected, $message);
    }

    final public static function assertLessThanOrEqual(mixed $expected, mixed $actual, string $message = ''): void
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @psalm-suppress MixedArgument
         */
        self::$assert
            ->number($actual)
            ->lessThanOrEqual($expected, $message);
    }

    final public static function assertLessThan(mixed $expected, mixed $actual, string $message = ''): void
    {
        /**
         * @psalm-suppress ArgumentTypeCoercion
         * @psalm-suppress MixedArgument
         */
        self::$assert
            ->number($actual)
            ->lessThan($expected, $message);
    }

    final public static function assertIsArray(mixed $actual, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert->true(\is_array($actual), $message);
    }

    final public static function assertStringStartsWith(string $prefix, string $string, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert
            ->string($string)
            ->startsWith($prefix, $message);
    }

    final public static function assertStringEndsWith(string $suffix, string $string, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert
            ->string($string)
            ->endsWith($suffix, $message);
    }

    final public static function assertStringContainsString(string $needle, string $haystack, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert
            ->string($haystack)
            ->contains($needle, $message);
    }

    final public static function assertContains(mixed $needle, iterable $haystack, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert
            ->expected($needle)
            ->in($haystack, $message);
    }

    final public static function assertNotContains(mixed $needle, iterable $haystack, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert
            ->expected($needle)
            ->not()
            ->in($haystack, $message);
    }

    final public static function assertIsInt(mixed $actual, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert
            ->number($actual)
            ->int($message);
    }

    final public static function assertIsString(mixed $actual, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert->true(\is_string($actual), $message);
    }

    final public static function assertMatchesRegularExpression(string $pattern, string $string, string $message = ''): void
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        self::$assert
            ->string($string)
            ->matches($pattern, $message);
    }

    final public function expectException(string $exception): void
    {
        $this->expectedException = $exception;
    }

    final public function expectExceptionCode(int|string $code): void
    {
        $this->expectedExceptionCode = $code;
    }

    final public function expectExceptionMessage(string $message): void
    {
        $this->expectedExceptionMessage = $message;
    }

    private function anExceptionIsExpected(): bool
    {
        return !\is_null($this->expectedException) || !\is_null($this->expectedExceptionCode) || !\is_null($this->expectedExceptionMessage);
    }
}
