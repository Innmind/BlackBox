<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert\Failure;

final class Truth
{
    /**
     * @param non-empty-string $message
     */
    private function __construct(private string $message)
    {
    }

    /**
     * @internal
     *
     * @param non-empty-string $message
     */
    public static function of(string $message): self
    {
        return new self($message);
    }

    /**
     * @return non-empty-string
     */
    public function message(): string
    {
        return $this->message;
    }
}
