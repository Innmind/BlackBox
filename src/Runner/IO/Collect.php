<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\IO;

use Innmind\BlackBox\Runner\IO;

/**
 * This class is used to test the printers
 */
final class Collect implements IO
{
    /** @var list<string> */
    private array $written;

    /**
     * @param list<string> $written
     */
    private function __construct(array $written)
    {
        $this->written = $written;
    }

    public function __invoke(string $data): void
    {
        $this->written[] = $data;
    }

    public static function new(): self
    {
        return new self([]);
    }

    /**
     * @return list<string>
     */
    public function written(): array
    {
        return $this->written;
    }

    public function toString(): string
    {
        return \implode('', $this->written);
    }
}
