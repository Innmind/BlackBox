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

    public function __construct()
    {
        $this->failures = Stream::of(Test\Report::class);
    }

    public function add(Test\Report $report): Report
    {
        if (!$report->failed()) {
            return $this;
        }

        $self = clone $this;
        $self->failures = $self->failures->add($report);

        return $self;
    }

    public function failures(): StreamInterface
    {
        return $this->failures;
    }
}
