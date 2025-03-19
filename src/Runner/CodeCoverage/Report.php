<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\CodeCoverage;

use Innmind\BlackBox\Runner\Proof\Name;
use SebastianBergmann\CodeCoverage\{
    Filter,
    Driver\Selector,
    CodeCoverage,
    Report\Clover,
};

/**
 * @internal
 */
final class Report
{
    private CodeCoverage $coverage;
    /** @var non-empty-string */
    private string $reportPath;

    /**
     * @param non-empty-list<non-empty-string> $directories
     * @param non-empty-string $reportPath
     */
    private function __construct(
        array $directories,
        string $reportPath,
    ) {
        $filter = new Filter;

        foreach ($directories as $directory) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory),
            );

            /**
             * @var string $path
             * @var \SplFileInfo $file
             */
            foreach ($files as $path => $file) {
                if ($file->isFile()) {
                    $filter->includeFile($path);
                }
            }
        }

        $this->coverage = new CodeCoverage(
            (new Selector)->forLineCoverage($filter),
            $filter,
        );
        $this->reportPath = $reportPath;
    }

    /**
     * @param non-empty-list<non-empty-string> $directories
     * @param non-empty-string $reportPath
     */
    public static function of(
        array $directories,
        string $reportPath,
    ): self {
        return new self($directories, $reportPath);
    }

    public function init(): void
    {
        $this->coverage->start('Proofs loader');
    }

    public function start(Name $proof): void
    {
        $this->coverage->start($proof->toString());
    }

    public function stop(): void
    {
        $this->coverage->stop();
    }

    public function dump(): void
    {
        (new Clover)->process($this->coverage, $this->reportPath);
    }
}
