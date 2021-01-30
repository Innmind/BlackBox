<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Printer;

use Innmind\BlackBox\Runner\{
    Printer,
    TestResult,
};
use SebastianBergmann\Timer\{
    ResourceUsageFormatter,
    Timer,
};

final class Simple implements Printer
{
    private const WIDTH = 50;
    private int $countProofs = 0;
    private int $countTestCases = 0;
    private int $countAssertions = 0;
    private int $countFailures = 0;
    /** @var list<array{proof: string, reason: string, result: TestResult, trace: list<string>}> */
    private array $failures = [];
    private Timer $timer;

    public function __construct()
    {
        $this->timer = new Timer;
    }

    public function begin(): void
    {
        $this->countProofs = 0;
        $this->countTestCases = 0;
        $this->countAssertions = 0;
        $this->countFailures = 0;
        $this->failures = [];
        $this->timer->start();
    }

    public function start(string $proof): void
    {
        ++$this->countProofs;
    }

    public function pass(string $proof): void
    {
        ++$this->countTestCases;
        echo '.';

        $this->breakLine();
    }

    public function held(): void
    {
        ++$this->countAssertions;
    }

    /**
     * @param list<string> $trace
     */
    public function fail(
        string $proof,
        string $reason,
        TestResult $result,
        array $trace
    ): void {
        ++$this->countTestCases;
        ++$this->countFailures;
        $this->failures[] = [
            'proof' => $proof,
            'reason' => $reason,
            'result' => $result,
            'trace' => $trace,
        ];
        echo 'F';

        $this->breakLine();
    }

    public function terminate(): bool
    {
        $duration = $this->timer->stop();

        if ($this->countFailures) {
            echo "\n\n";
            echo \str_repeat('-', self::WIDTH)."\n\n";
        }

        foreach ($this->failures as $failure) {
            echo '"'.$failure['proof'].'" invalidated'.":\n";
            echo "{$failure['reason']}\n";
            echo "\n";
            echo "Caused by:\n";
            /** @psalm-suppress MissingClosureParamType */
            $failure['result']->arguments()(static function(string $argument, $value): void {
                echo "\$$argument = ";
                \var_export($value);
                echo ";\n";
            });
            echo "\n";
            $thrown = $failure['result']->thrown() ? ' (thrown)' : '';
            echo "Generated$thrown: \n";
            \var_export($failure['result']->value());
            echo "\n";
            echo "\n";

            foreach ($failure['trace'] as $frame) {
                echo "$frame\n";
            }

            echo "\n";
            echo \str_repeat('-', self::WIDTH)."\n";
        }

        echo "\n";
        echo "Proofs:               {$this->countProofs}\n";
        echo "Test cases generated: {$this->countTestCases}\n";
        echo "Assertions:           {$this->countAssertions}\n";
        echo "Failures:             {$this->countFailures}\n\n";
        echo (new ResourceUsageFormatter)->resourceUsage($duration)."\n";

        return $this->countFailures > 0;
    }

    private function breakLine(): void
    {
        if ($this->countTestCases % self::WIDTH === 0) {
            echo "\n";
        }
    }
}
