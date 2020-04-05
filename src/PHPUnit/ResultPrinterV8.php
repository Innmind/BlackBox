<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\Set\Value;
use PHPUnit\TextUI\ResultPrinter;
use PHPUnit\Framework\{
    TestFailure,
    ExceptionWrapper,
};
use Symfony\Component\VarDumper\{
    Dumper\CliDumper,
    Cloner\VarCloner,
};

/**
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress DeprecatedInterface
 * @psalm-suppress InternalClass
 */
final class ResultPrinterV8 extends ResultPrinter
{
    private static ?self $currentInstance = null;
    /** @var array<string, Value> */
    private array $dataSets = [];
    private VarCloner $cloner;
    private CliDumper $dumper;

    /**
     * @psalm-suppress InternalClass
     * @psalm-suppress InternalMethod
     * @psalm-suppress MissingParamType
     * @psalm-suppress MixedArgument
     */
    public function __construct($out, ...$args)
    {
        parent::__construct($out, ...$args);
        $this->cloner = new VarCloner;
        $this->dumper = new CliDumper($out);
        self::$currentInstance = $this;
    }

    public static function record(\Throwable $e, Value $value): void
    {
        if (\is_null(self::$currentInstance)) {
            return;
        }

        $hash = self::$currentInstance->hash($e);
        self::$currentInstance->dataSets[$hash] = $value;
    }

    protected function printDefect(TestFailure $defect, int $count): void
    {
        /** @psalm-suppress InternalMethod */
        $this->printDefectHeader($defect, $count);
        $this->printDataSet($this->exceptionFrom($defect));
        /** @psalm-suppress InternalMethod */
        $this->printDefectTrace($defect);
    }

    private function printDataSet(\Throwable $e): void
    {
        if (!\array_key_exists($this->hash($e), $this->dataSets)) {
            return;
        }

        /** @psalm-suppress InternalMethod */
        $this->write("Test failing with the following set of values : \n");

        /** @psalm-suppress MixedAssignment */
        foreach ($this->dataSets[$this->hash($e)]->unwrap() as $value) {
            $this->dump($value);
        }

        /** @psalm-suppress InternalMethod */
        $this->write("\n");
    }

    protected function printDefectTrace(TestFailure $defect): void
    {
        /** @psalm-suppress InternalMethod */
        $e = $defect->thrownException();
        $trace = \explode("\n", (string) $e);
        $trace = \array_filter(
            $trace,
            fn(string $line): bool => \strpos($line, 'innmind/black-box/src/PHPUnit/TestRunner.php') === false,
        );
        $trace = \implode("\n", $trace);

        /** @psalm-suppress InternalMethod */
        $this->write($trace);

        while ($e = $e->getPrevious()) {
            /** @psalm-suppress InternalMethod */
            $this->write("\nCaused by\n" . $e);
        }
    }

    /** @psalm-suppress MissingParamType */
    private function dump($var): void
    {
        $this->dumper->dump($this->cloner->cloneVar($var));
    }

    private function hash(\Throwable $e): string
    {
        return \spl_object_hash($e);
    }

    private function exceptionFrom(TestFailure $defect): \Throwable
    {
        /** @psalm-suppress InternalMethod */
        $thrownException = $defect->thrownException();

        if ($thrownException instanceof ExceptionWrapper) {
            /** @psalm-suppress InternalMethod */
            $originalException = $thrownException->getOriginalException();

            if ($originalException instanceof \Throwable) {
                return $originalException;
            }
        }

        return $thrownException;
    }
}
