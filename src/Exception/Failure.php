<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Exception;

use Innmind\BlackBox\Runner\Arguments;

final class Failure extends RuntimeException
{
    private string $reason;
    private Arguments $arguments;

    public function __construct(string $reason, Arguments $arguments)
    {
        parent::__construct($reason);
        $this->reason = $reason;
        $this->arguments = $arguments;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function arguments(): Arguments
    {
        return $this->arguments;
    }
}
