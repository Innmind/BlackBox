<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Printer;

use Innmind\BlackBox\Runner\{
    Proof\Scenario\Failure,
    Assert\Failure\Truth,
    Assert\Failure\Property,
    Assert\Failure\Comparison,
    IO,
};
use Symfony\Component\VarDumper\{
    Dumper\CliDumper,
    Cloner\VarCloner,
};

/**
 * @internal
 */
final class Proof
{
    private function __construct(
        private CliDumper $dumper,
        private VarCloner $cloner,
        private bool $addMarks,
        private bool $addGroups,
        private int $scenarii = 0,
    ) {
    }

    /**
     * @internal
     */
    public static function new(
        bool $addMarks,
        bool $addGroups,
    ): self {
        $dumper = new CliDumper;
        $dumper->setColors(true);
        $cloner = new VarCloner;
        $cloner->setMinDepth(100);

        return new self(
            $dumper,
            $cloner,
            $addMarks,
            $addGroups,
        );
    }

    public function withoutColors(): self
    {
        $dumper = new CliDumper;
        $dumper->setColors(false);

        return new self(
            $dumper,
            $this->cloner,
            $this->addMarks,
            $this->addGroups,
        );
    }

    public function disableGitHubOutput(): self
    {
        return new self(
            $this->dumper,
            $this->cloner,
            $this->addMarks,
            false,
        );
    }

    public function emptySet(IO $output): void
    {
        $output("No scenario found\n");

        if ($this->addGroups) {
            $output("::endgroup::\n");
        }
    }

    public function success(IO $output): void
    {
        $this->newLine($output);
        $output('.');
    }

    public function shrunk(IO $output): void
    {
        $this->newLine($output);
        $output('S');
    }

    public function failed(IO $output, Failure $failure): void
    {
        $this->newLine($output);
        $output("F\n\n");
        $this->renderScenario($output, $failure);

        $output("\n");

        $this->renderFailure($output, $failure->assertion()->kind());

        $output(\sprintf(
            "\n%s%s\n",
            match ($this->addGroups) {
                true => '::error ::',
                false => '',
            },
            $failure->assertion()->kind()->message(),
        ));

        /**
         * @var list<array{
         *      file?: string,
         *      line?: int,
         * }> $frame
         */
        $trace = $failure->assertion()->getTrace();
        $output("\n");

        if ($this->addMarks) {
            $output("\x1b]1337;SetMark\x07");
        }

        foreach ($trace as $frame) {
            if (!\array_key_exists('file', $frame)) {
                continue;
            }

            if (!\array_key_exists('line', $frame)) {
                continue;
            }

            // do not render the internal calls to the runner, this path is the
            // one either locally on my machine or as a project dependency via
            // composer
            if (
                \str_contains($frame['file'], 'innmind/black-box/src/Runner') ||
                \str_contains($frame['file'], 'innmind/black-box/src/Application.php') ||
                \str_contains($frame['file'], 'innmind/black-box/src/PHPUnit')
            ) {
                continue;
            }

            // same goal as above but for the GitHub Actions
            if (
                \str_contains($frame['file'], '/runner/work/BlackBox/BlackBox/src/Runner') ||
                \str_contains($frame['file'], '/runner/work/BlackBox/BlackBox/src/Application.php') ||
                \str_contains($frame['file'], '/runner/work/BlackBox/BlackBox/src/PHPUnit')
            ) {
                continue;
            }

            $output(\sprintf(
                "%s:%s\n",
                $frame['file'],
                $frame['line'],
            ));
        }
    }

    public function end(IO $output): void
    {
        $this->scenarii = 0;
        $output("\n\n");

        if ($this->addGroups) {
            $output("::endgroup::\n");
        }
    }

    private function newLine(IO $output): void
    {
        if ($this->scenarii !== 0 && $this->scenarii % 50 === 0) {
            $output("\n");
        }

        ++$this->scenarii;
    }

    private function renderFailure(
        IO $output,
        Truth|Property|Comparison $failure,
    ): void {
        if ($failure instanceof Truth) {
            return;
        }

        if ($failure instanceof Property) {
            $output(\sprintf(
                '$variable = %s',
                $this->dump($failure->value()),
            ));

            return;
        }

        $output(\sprintf(
            '$expected = %s',
            $this->dump($failure->expected()),
        ));
        $output(\sprintf(
            '$actual = %s',
            $this->dump($failure->actual()),
        ));
    }

    private function dump(mixed $value): string
    {
        return $this->dumper->dump(
            $this->cloner->cloneVar(
                $value,
            ),
            true,
        ) ?? '';
    }

    private function renderScenario(IO $output, Failure $failure): void
    {
        /** @var mixed $value */
        foreach ($failure->parameters() as [$name, $value]) {
            $output(\sprintf(
                '$%s = ',
                $name,
            ));
            $output($this->dump($value));
        }
    }
}
