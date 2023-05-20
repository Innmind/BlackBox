<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Runner\Printer;

use Innmind\BlackBox\Runner\Failure;

interface Proof
{
    public function emptySet(): void;
    public function success(): void;
    public function shrunk(): void;
    public function failed(Failure $failure): void;
    public function end(): void;
}
