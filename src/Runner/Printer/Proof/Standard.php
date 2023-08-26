<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Printer\Proof;

use Innmind\BlackBox\Runner\{
    Proof\Scenario\Failure,
    Assert\Failure\Truth,
    Assert\Failure\Property,
    Assert\Failure\Comparison,
    IO,
    Printer\Proof,
    Proof\Scenario,
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
    private int $scenarii = 0;

    private function __construct(bool $withColors)
    {
        $this->dumper = new CliDumper;
        $this->cloner = new VarCloner;
        $this->dumper->setColors($withColors);
    }

    public static function new(bool $withColors): self
    {
        return new self($withColors);
    }

    public function emptySet(IO $output, IO $error): void
    {
        $error("No scenario found\n");
    }

    public function success(IO $output, IO $error): void
    {
        $this->newLine($output);
        $output('.');
    }

    public function shrunk(IO $output, IO $error): void
    {
        $this->newLine($output);
        $output('S');
    }

    public function failed(IO $output, IO $error, Failure $failure): void
    {
        $this->newLine($output);
        $output("F\n\n");
        $this->renderScenario($output, $failure->scenario()->unwrap());

        $output("\n");

        $this->renderFailure($output, $failure->assertion()->kind());

        $output(\sprintf(
            "\n%s\n",
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
                \str_contains($frame['file'], '/home/runner/work/BlackBox/BlackBox/src/Runner') ||
                \str_contains($frame['file'], '/home/runner/work/BlackBox/BlackBox/src/Application.php') ||
                \str_contains($frame['file'], '/home/runner/work/BlackBox/BlackBox/src/PHPUnit')
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

    public function end(IO $output, IO $error): void
    {
        $output("\n\n");
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
        if ($scenario instanceof Scenario\Inline) {
            $this->renderInlineScenario($output, $scenario);
        }

        if ($scenario instanceof Scenario\Property) {
            $this->renderPropertyScenario($output, $scenario);
        }

        if ($scenario instanceof Scenario\Properties) {
            $this->renderPropertiesScenario($output, $scenario);
        }
    }

    private function renderInlineScenario(IO $output, Scenario\Inline $scenario): void
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
