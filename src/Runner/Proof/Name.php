<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Proof;

/**
 * @psalm-immutable
 */
final class Name
{
    /** @var non-empty-string */
    private string $name;

    /**
     * @param non-empty-string $name
     */
    private function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @psalm-pure
     *
     * @param non-empty-string $name
     */
    public static function of(string $name): self
    {
        return new self($name);
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->name;
    }
}
