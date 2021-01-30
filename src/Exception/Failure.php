<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Exception;

use Innmind\BlackBox\Runner\TestResult;

final class Failure extends RuntimeException
{
    private string $reason;
    private TestResult $result;

    public function __construct(string $reason, TestResult $result)
    {
        parent::__construct($reason);
        $this->reason = $reason;
        $this->result = $result;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function result(): TestResult
    {
        return $this->result;
    }
}
