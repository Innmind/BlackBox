<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Value;

/**
 * @internal
 *
 * This is a flag to notify the shrinking mechanisms that the value can't be
 * shrunk any further.
 */
enum End
{
    case instance;
}
