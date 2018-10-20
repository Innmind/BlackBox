<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Suites\Report;

use Innmind\BlackBox\{
    Suites\Report,
    Test,
};
use Innmind\Immutable\{
    StreamInterface,
    Stream,
};

final class InMemory implements Report
{
    private $failures;
    private $assertions = 0;
    private $tests = 0;

    public function __construct()
    {
        $this->failures = Stream::of(Test\Report::class);
    }

    public function add(Test\Report $report): Report
    {
        $self = clone $this;
        ++$self->tests;
        $self->assertions += $report->assertions();

        if ($report->failed()) {
            $self->failures = $self->failures->add($report);
        }

        return $self;
    }

    public function failures(): StreamInterface
    {
        return $this->failures;
    }

    public function assertions(): int
    {
        return $this->assertions;
    }

    public function tests(): int
    {
        return $this->tests;
    }
}
