<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\When;

final class Result
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function value()
    {
        return $this->value;
    }
}
