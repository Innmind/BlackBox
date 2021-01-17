<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class TestResult
{
    /** @var mixed */
    private $value;
    private bool $thrown;
    private Arguments $arguments;

    /**
     * @param mixed $value
     */
    private function __construct($value, bool $thrown, Arguments $arguments)
    {
        $this->value = $value;
        $this->thrown = $thrown;
        $this->arguments = $arguments;
    }

    /**
     * @param mixed $value
     */
    public static function of($value, Arguments $arguments): self
    {
        return new self($value, false, $arguments);
    }

    public static function throws(\Throwable $value, Arguments $arguments): self
    {
        return new self($value, true, $arguments);
    }

    /**
     * Whether the value has been thrown or not
     */
    public function thrown(): bool
    {
        return $this->thrown;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * @internal
     */
    public function arguments(): Arguments
    {
        return $this->arguments;
    }
}
