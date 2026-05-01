<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Assert;

final class Debug
{
    /**
     * @param array<non-empty-string, mixed> $data
     */
    private function __construct(
        private array $data,
    ) {
    }

    /**
     * @internal
     */
    public static function new(): self
    {
        return new self([]);
    }

    /**
     * @internal
     */
    public function reset(): void
    {
        $this->data = [];
    }

    /**
     * @internal
     *
     * @param non-empty-string $name
     */
    public function add(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * @return list<array{non-empty-string, mixed}>
     */
    public function parameters(): array
    {
        $all = [];

        /** @var mixed $value */
        foreach ($this->data as $name => $value) {
            $all[] = [$name, $value];
        }

        return $all;
    }
}
