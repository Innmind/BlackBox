<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Test\Report;
use Innmind\Url\PathInterface;

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
    public function __invoke(PathInterface $path): \Generator
    {
        foreach (($this->load)($path) as $test) {
            yield ($this->run)($test);
        }
    }
}
