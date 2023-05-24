<?php

use Innmind\CodingStandard\CodingStandard;

// disable the strict comparison as it's needed for Runner\Assert
return CodingStandard::config(
    ['tests', 'src', 'proofs'],
    \array_merge(
        CodingStandard::rules(),
        ['strict_comparison' => false],
    ),
);
