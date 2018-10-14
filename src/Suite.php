<?php
declare(strict_types = 1);

namespace Innmind\BlackBox;

use Innmind\BlackBox\Test\Report;
use Innmind\Url\PathInterface;
use Innmind\Immutable\{
    StreamInterface,
    Stream,
};

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
     * @return StreamInterface<Report>
     */
    public function __invoke(PathInterface $path): StreamInterface
    {
        return ($this->load)($path)->reduce(
            Stream::of(Report::class),
            function(StreamInterface $reports, \Generator $tests): StreamInterface {
                foreach ($tests as $test) {
                    $reports = $reports->add(
                        ($this->run)($test)
                    );
                }

                return $reports;
            }
        );
    }
}
