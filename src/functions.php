<?php

declare(strict_types=1);

namespace Flying\Util\Css;

if (\function_exists(classes::class)) {
    return;
}

/**
 * Create a string of CSS classes from provided classes information
 *
 * @param mixed ...$classes
 * @return string
 */
function classes(mixed ...$classes): string
{
    return (string)Classes::from(...$classes);
}
