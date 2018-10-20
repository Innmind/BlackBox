<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Suites\Report;

use Innmind\BlackBox\{
    Suites\Report,
    Test,
};
use Innmind\Stream\Writable;
use Innmind\Immutable\{
    StreamInterface,
    Str,
};

final class Printer implements Report
{
    private $stream;
    private $report;
    private $reported = 0;

    public function __construct(Writable $stream, Report $report)
    {
        $this->stream = $stream;
        $this->report = $report;
    }

    public function add(Test\Report $report): Report
    {
        $this->report = $this->report->add($report);
        $this->print($report);

        return $this;
    }

    public function failures(): StreamInterface
    {
        return $this->report->failures();
    }

    public function assertions(): int
    {
        return $this->report->assertions();
    }

    public function tests(): int
    {
        return $this->report->tests();
    }

    private function print(Test\Report $report): void
    {
        $this->stream->write(Str::of(
            $report->failed() ? 'F' : '.'
        ));
        ++$this->reported;

        if ($this->reported % 50 === 0) {
            $this->stream->write(Str::of("\n"));
        }
    }
}
