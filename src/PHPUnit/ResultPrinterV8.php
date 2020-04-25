<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\PHPUnit;

use Innmind\BlackBox\{
    Set\Value,
    Properties,
    Property,
};
use PHPUnit\TextUI\ResultPrinter;
use PHPUnit\Framework\{
    TestFailure,
    SelfDescribing,
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

    public static function record(object $test, \Throwable $e, Value $value): void
    {
        if (\is_null(self::$currentInstance)) {
            return;
        }

        $hash = self::$currentInstance->hash($test, $e);
        self::$currentInstance->dataSets[$hash] = $value;
    }

    protected function printDefect(TestFailure $defect, int $count): void
    {
        /** @psalm-suppress InternalMethod */
        $this->printDefectHeader($defect, $count);
        $this->printDataSet($defect);
        /** @psalm-suppress InternalMethod */
        $this->printDefectTrace($defect);
    }

    private function printDataSet(TestFailure $defect): void
    {
        $values = $this->findFailingValues($defect);

        if (\is_null($values)) {
            return;
        }

        /** @psalm-suppress InternalMethod */
        $this->write("Test failing with the following set of values : \n");

        /** @psalm-suppress MixedAssignment */
        foreach ($values->unwrap() as $value) {
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
            fn(string $line): bool => \strpos($line, 'innmind/black-box/src/PHPUnit/') === false,
        );
        $trace = \array_filter(
            $trace,
            fn(string $line): bool => \strpos($line, '/home/runner/work/BlackBox/BlackBox/src/PHPUnit/TestRunner.php') === false,
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
        if ($var instanceof Properties) {
            /** @psalm-suppress InternalMethod */
            $this->write('list<Property>: ');
            $var = \array_map(
                static fn(Property $property): string => $property->name(),
                $var->properties(),
            );
        }

        $this->dumper->dump($this->cloner->cloneVar($var));
    }

    private function hash(object $test, \Throwable $e): string
    {
        if ($test instanceof SelfDescribing) {
            return $test->toString();
        }

        return \spl_object_hash($e);
    }

    private function findFailingValues(TestFailure $defect): ?Value
    {
        /** @psalm-suppress InternalMethod */
        $test = $defect->failedTest() ?: new \stdClass;
        $hash = $this->hash(
            $test,
            $this->exceptionFrom($defect),
        );

        if (!\array_key_exists($hash, $this->dataSets)) {
            return null;
        }

        return $this->dataSets[$hash];
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
