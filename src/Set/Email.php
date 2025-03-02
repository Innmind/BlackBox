<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set;

use Innmind\BlackBox\Set;

final class Email
{
    /**
     * @deprecated Use Set::email() instead
     * @psalm-pure
     *
     * @return Set<non-empty-string>
     */
    public static function any(): Set
    {
        return Set::email();
    }
}
