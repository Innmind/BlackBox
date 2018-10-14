<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Then;

use Innmind\BlackBox\Exception\LogicException;

final class ScenarioReport
{
    private $assertions = 0;
    private $failure;

    public function success(): self
    {
        if ($this->failed()) {
            throw new LogicException('No success should be reported after a failure');
        }

        $self = clone $this;
        ++$self->assertions;

        return $self;
    }

    public function fail(string $message): self
    {
        if ($this->failed()) {
            throw new LogicException('Only one failure can be reported for a scenario');
        }

        $self = clone $this;
        $self->failure = $message;
        ++$self->assertions;

        return $self;
    }

    public function failure(): string
    {
        return $this->failure;
    }

    public function failed(): bool
    {
        return is_string($this->failure);
    }

    public function assertions(): int
    {
        return $this->assertions;
    }
}
