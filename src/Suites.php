<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Suites\Report;
use Innmind\Url\PathInterface;
use Innmind\Immutable\Sequence;

final class Suites
{
    private $suite;

    public function __construct(Suite $suite)
    {
        $this->suite = $suite;
    }

    public function __invoke(Report $report, PathInterface ...$paths): Report
    {
        return Sequence::of(...$paths)->reduce(
            $report,
            function(Report $report, PathInterface $path): Report {
                return ($this->suite)($path)->reduce(
                    $report,
                    static function(Report $report, Test\Report $testReport): Report {
                        return $report->add($testReport);
                    }
                );
            }
        );
    }
}
