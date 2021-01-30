<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Exception;

use Innmind\BlackBox\Runner\TestResult;

final class Failure extends RuntimeException
{
    private string $reason;
    private TestResult $result;
    /** @var list<string> */
    private array $trace;

    /**
     * @param list<string> $trace
     */
    public function __construct(
        string $reason,
        TestResult $result,
        array $trace
    ) {
        parent::__construct($reason);
        $this->reason = $reason;
        $this->result = $result;
        $this->trace = $trace;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function result(): TestResult
    {
        return $this->result;
    }

    /**
     * @return list<string>
     */
    public function trace(): array
    {
        return $this->trace;
    }
}
