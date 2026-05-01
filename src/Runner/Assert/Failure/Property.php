<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert\Failure;

final class Property
{
    /**
     * @param non-empty-string $message
     */
    private function __construct(
        private mixed $value,
        private string $message,
    ) {
    }

    /**
     * @internal
     *
     * @param non-empty-string $message
     */
    public static function of(mixed $value, string $message): self
    {
        return new self($value, $message);
    }

    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * @return non-empty-string
     */
    public function message(): string
    {
        return $this->message;
    }
}
