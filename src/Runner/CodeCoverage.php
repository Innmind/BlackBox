<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class CodeCoverage
{
    /** @var non-empty-list<non-empty-string> */
    private array $directories;
    /** @var ?non-empty-string */
    private ?string $reportPath;

    /**
     * @psalm-mutation-free
     *
     * @param non-empty-list<non-empty-string> $directories
     * @param ?non-empty-string $reportPath
     */
    private function __construct(
        array $directories,
        ?string $reportPath,
    ) {
        $this->directories = $directories;
        $this->reportPath = $reportPath;
    }

    /**
     * @psalm-pure
     * @no-named-arguments
     *
     * @param non-empty-string $directory
     * @param non-empty-string $directories
     */
    #[\NoDiscard]
    public static function of(string $directory, string ...$directories): self
    {
        return new self([$directory, ...$directories], null);
    }

    /**
     * @psalm-mutation-free
     *
     * @param non-empty-string $path
     */
    #[\NoDiscard]
    public function dumpTo(string $path): self
    {
        return new self($this->directories, $path);
    }

    /**
     * @internal
     */
    public function build(): ?CodeCoverage\Report
    {
        if (\is_null($this->reportPath)) {
            return null;
        }

        return CodeCoverage\Report::of($this->directories, $this->reportPath);
    }
}
