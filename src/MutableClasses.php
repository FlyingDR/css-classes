<?php

namespace Flying\Util\Css;

class MutableClasses extends AbstractClasses
{
    public static function from(mixed ...$classes): static
    {
        return new self(...$classes);
    }

    public function with(mixed ...$classes): static
    {
        $this->classes = array_merge($this->classes, $this->classesList($classes));
        return $this;
    }

    public function without(mixed ...$classes): static
    {
        $classes = $this->classesList($classes);
        return $this->filter(static fn(string $class): bool => !array_key_exists($class, $classes));
    }

    public function filter(callable $filter): static
    {
        $this->classes = array_filter($this->classes, $filter, ARRAY_FILTER_USE_KEY);
        return $this;
    }

    public function clear(): static
    {
        $this->classes = [];
        return $this;
    }
}
