<?php

namespace Flying\Util\Css;

/**
 * Helper class to simplify construction of list of CSS classes
 */
class Classes implements \Countable, \IteratorAggregate, \Stringable
{
    private array $classes;

    public function __construct(mixed ...$classes)
    {
        if (count($classes) === 1 && $classes[0] instanceof self) {
            // Optimize a case when new instance is created from single instance of Classes
            $this->classes = $classes[0]->classes;
        } else {
            $this->classes = $this->classesList($classes);
        }
    }

    public static function from(mixed ...$classes): self
    {
        return new self(...$classes);
    }

    private function clone(array $classes): self
    {
        $instance = new self();
        $instance->classes = $classes;
        return $instance;
    }

    public function with(mixed ...$classes): self
    {
        return $this->clone(array_merge($this->classes, $this->classesList($classes)));
    }

    public function without(mixed ...$classes): self
    {
        $classes = $this->classesList($classes);
        return $this->filter(static fn(string $class): bool => !array_key_exists($class, $classes));
    }

    public function filter(callable $filter): self
    {
        return $this->clone(array_filter($this->classes, $filter, ARRAY_FILTER_USE_KEY));
    }

    public function clear(): self
    {
        return $this->clone([]);
    }

    public function count(): int
    {
        return count($this->classes);
    }

    public function has(string $class): bool
    {
        return array_key_exists($class, $this->classes);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->toArray());
    }

    public function toArray(): array
    {
        return array_keys($this->classes);
    }

    public function __toString(): string
    {
        return implode(' ', $this->toArray());
    }

    private function classesList(iterable $classes): array
    {
        $iterator = function (iterable $classes): iterable {
            foreach ($classes as $class) {
                if (is_iterable($class)) {
                    yield from array_keys($this->classesList($class));
                } elseif (is_string($class)) {
                    yield from preg_split('/\s+/', $class);
                }
            }
        };
        $result = [];
        foreach ($iterator($classes) as $class) {
            $result[trim($class)] = true;
        }
        return $result;
    }
}
