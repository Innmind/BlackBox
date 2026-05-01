<?php
declare(strict_types = 1);

namespace Fixtures\Innmind\BlackBox;

final class Counter
{
    private bool $correct = true;

    public function __construct(private int $value = 0)
    {
    }

    public static function failOnPurpose(): self
    {
        $self = new self;
        $self->correct = false;

        return $self;
    }

    public function down(): void
    {
        if ($this->correct && $this->value === 0) {
            return;
        }

        --$this->value;
    }

    public function up(): void
    {
        if ($this->correct && $this->value === 100) {
            return;
        }

        ++$this->value;
    }

    public function current(): int
    {
        return $this->value;
    }
}
