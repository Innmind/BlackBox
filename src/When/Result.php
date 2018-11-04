<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\When;

use Innmind\TimeContinuum\ElapsedPeriodInterface;

final class Result
{
    private $value;
    private $executionTime;

    public function __construct($value, ElapsedPeriodInterface $executionTime)
    {
        $this->value = $value;
        $this->executionTime = $executionTime;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }

    public function executionTime(): ElapsedPeriodInterface
    {
        return $this->executionTime;
    }
}
