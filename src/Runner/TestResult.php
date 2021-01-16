<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner;

final class TestResult
{
    /** @var mixed */
    private $value;
    private bool $thrown;

    /**
     * @param mixed $value
     */
    private function __construct($value, bool $thrown)
    {
        $this->value = $value;
        $this->thrown = $thrown;
    }

    /**
     * @param mixed $value
     */
    public static function of($value): self
    {
        return new self($value, false);
    }

    public static function throws(\Throwable $value): self
    {
        return new self($value, true);
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
}
