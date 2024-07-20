# Shrinking

## `Sequence`

```mermaid
graph TB
    Array -->|Next| RecursiveHalf{Either}
    RecursiveHalf -->|a| RemoveHalf
    RecursiveHalf -->|b| RemoveTail
    RemoveHalf -->|Next| RecursiveHalf
    RemoveTail -->|Next| RecursiveTail{Either}
    RecursiveTail -->|a| RemoveTail
    RecursiveTail -->|b| RemoveHead
    RemoveHead -->|Next| RecursiveHead{Either}
    RecursiveHead -->|a| RemoveHead
    RecursiveHead -->|b| RemoveNth
    RemoveNth -->|Next| RecursiveNth{Either}
    RecursiveNth -->|"a(n)"| RemoveNth
    RecursiveNth -->|"b(n+1)"| RemoveNth
    RemoveNth -->|When n overflows| ShrinkANth[Shrink nth element with strategy A]
    ShrinkANth -->|Next| RecursiveNthShrink{Either}
    RecursiveNthShrink -->|"a(n)"| ShrinkANth
    RecursiveNthShrink -->|"b(n+1)"| ShrinkANth
    ShrinkANth -->|When n overflows| ShrinkBNth[Shrink nth element with strategy B]
    ShrinkBNth -->|Next| RecursiveNthShrink
    RecursiveNthShrink -->|When no longer shrinkable| Identity
```

This design makes sure all elements are shrunk to their minimum values. It's assured when shrinking elements of the `array` with their `a` or `b` strategy. When we apply a strategy makes the test pass we try to shrink the next available value in the array. This privileges first shrinking values that do not affect the test.

This strategy means the values that affect the failing test are shrunk last. So it will take more shrinking steps to find the minimum values that make a test fail.
