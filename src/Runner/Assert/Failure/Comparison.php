<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert\Failure;

final class Comparison
{
    /**
     * @param non-empty-string $message
     */
    private function __construct(
        private mixed $expected,
        private mixed $actual,
        private string $message,
    ) {
    }

    /**
     * @internal
     *
     * @param non-empty-string $message
     */
    public static function of(
        mixed $expected,
        mixed $actual,
        string $message,
    ): self {
        return new self($expected, $actual, $message);
    }

    public function expected(): mixed
    {
        return $this->expected;
    }

    public function actual(): mixed
    {
        return $this->actual;
    }

    /**
     * @return non-empty-string
     */
    public function message(): string
    {
        return $this->message;
    }
}
