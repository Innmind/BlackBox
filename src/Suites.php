<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Suites\Report;
use Innmind\Url\PathInterface;

final class Suites
{
    private $suite;

    public function __construct(Suite $suite)
    {
        $this->suite = $suite;
    }

    public function __invoke(Report $report, PathInterface ...$paths): Report
    {
        foreach ($paths as $path) {
            $testsReport = ($this->suite)($path);

            foreach ($testsReport as $testReport) {
                $report->add($testReport);
            }
        }

        return $report;
    }
}
