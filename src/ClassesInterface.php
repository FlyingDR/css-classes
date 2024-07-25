<?php

declare(strict_types=1);

namespace Flying\Util\Css;

/**
 * Interface for implementations of the set of CSS classes
 */
interface ClassesInterface extends \Countable, \IteratorAggregate, \Stringable
{
    public static function from(mixed ...$classes): static;

    public function with(mixed ...$classes): static;

    public function without(mixed ...$classes): static;

    /**
     * @param callable(string): boolean $filter
     * @return static
     */
    public function filter(callable $filter): static;

    public function clear(): static;

    public function has(string $class): bool;

    public function getIterator(): \ArrayIterator;

    public function toArray(): array;
}
