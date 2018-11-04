<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Test\Report;
use Innmind\Url\PathInterface;
use Innmind\OperatingSystem\OperatingSystem;

final class Suite
{
    private $load;
    private $run;

    public function __construct(Loader $load, Runner $run)
    {
        $this->load = $load;
        $this->run = $run;
    }

    /**
     * @return \Generator<Report>
     */
    public function __invoke(
        OperatingSystem $os,
        PathInterface $path
    ): \Generator {
        foreach (($this->load)($path) as $test) {
            yield ($this->run)($os, $test);
        }
    }
}
