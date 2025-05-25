<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Printer\Proof;

use Innmind\BlackBox\{
    Runner\Proof\Scenario\Failure,
    Runner\Assert\Failure\Truth,
    Runner\Assert\Failure\Property,
    Runner\Assert\Failure\Comparison,
    Runner\IO,
    Runner\Printer\Proof,
    Runner\Proof\Scenario,
    PHPUnit\Proof\Bridge,
};
use Symfony\Component\VarDumper\{
    Dumper\CliDumper,
    Cloner\VarCloner,
};

/**
 * @internal
 */
final class Standard implements Proof
{
    private CliDumper $dumper;
    private VarCloner $cloner;
    private bool $addMarks;
    private bool $addGroups;
    private int $scenarii = 0;

    private function __construct(
        bool $withColors,
        bool $addMarks,
        bool $addGroups,
    ) {
        $this->dumper = new CliDumper;
        $this->cloner = new VarCloner;
        $this->addMarks = $addMarks;
        $this->addGroups = $addGroups;
        $this->dumper->setColors($withColors);
        $this->cloner->setMinDepth(100);
    }

    public static function new(
        bool $withColors,
        bool $addMarks,
        bool $addGroups,
    ): self {
        return new self($withColors, $addMarks, $addGroups);
    }

    #[\Override]
    public function emptySet(IO $output, IO $error): void
    {
        $error("No scenario found\n");

        if ($this->addGroups) {
            $output("::endgroup::\n");
        }
    }

    #[\Override]
    public function success(IO $output, IO $error): void
    {
        $this->newLine($output);
        $output('.');
    }

    #[\Override]
    public function shrunk(IO $output, IO $error): void
    {
        $this->newLine($output);
        $output('S');
    }

    #[\Override]
    public function failed(IO $output, IO $error, Failure $failure): void
    {
        $this->newLine($output);
        $output("F\n\n");
        $this->renderScenario($output, $failure->scenario()->unwrap());

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

    #[\Override]
    public function end(IO $output, IO $error): void
    {
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

    private function renderScenario(IO $output, Scenario $scenario): void
    {
        if (
            $scenario instanceof Scenario\Inline ||
            $scenario instanceof Bridge
        ) {
            $this->renderInlineScenario($output, $scenario);
        }

        if ($scenario instanceof Scenario\Property) {
            $this->renderPropertyScenario($output, $scenario);
        }

        if ($scenario instanceof Scenario\Properties) {
            $this->renderPropertiesScenario($output, $scenario);
        }
    }

    private function renderInlineScenario(IO $output, Scenario\Inline|Bridge $scenario): void
    {
        /** @var mixed $value */
        foreach ($scenario->parameters() as [$name, $value]) {
            $output(\sprintf(
                '$%s = ',
                $name,
            ));
            $output($this->dump($value));
        }
    }

    private function renderPropertyScenario(IO $output, Scenario\Property $scenario): void
    {
        $output('$property = ');
        $output($this->dump($scenario->property()));
        $output('$systemUnderTest = ');
        $output($this->dump($scenario->systemUnderTest()));
    }

    private function renderPropertiesScenario(IO $output, Scenario\Properties $scenario): void
    {
        $output('$properties = ');
        $output($this->dump($scenario->properties()));
        $output('$systemUnderTest = ');
        $output($this->dump($scenario->systemUnderTest()));
    }
}
