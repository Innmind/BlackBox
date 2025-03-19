<?php
declare(strict_types = 1);

namespace Innmind\BlackBox\Set\Sequence;

/**
 * @internal
 */
enum Strategy
{
    case recursiveHalf;
    case recursiveTail;
    case recursiveHead;
    case recursiveNth;
    case recursiveNthShrink;
}
