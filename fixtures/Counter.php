<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

final class Counter
{
    private int $value;

    public function __construct(int $initial = 0)
    {
        $this->value = $initial;
    }

    public function down(): void
    {
        if ($this->value === 0) {
            return;
        }

        --$this->value;
    }

    public function up(): void
    {
        if ($this->value === 100) {
            return;
        }

        ++$this->value;
    }

    public function current(): int
    {
        return $this->value;
    }
}
