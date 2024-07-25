<?php

namespace Flying\Util\Css;

class Classes extends AbstractClasses
{
    public static function from(mixed ...$classes): static
    {
        return new self(...$classes);
    }

    private function clone(array $classes): self
    {
        $instance = new self();
        $instance->classes = $classes;
        return $instance;
    }

    public function with(mixed ...$classes): static
    {
        return $this->clone(array_merge($this->classes, $this->classesList($classes)));
    }

    public function without(mixed ...$classes): static
    {
        $classes = $this->classesList($classes);
        return $this->filter(static fn(string $class): bool => !array_key_exists($class, $classes));
    }

    public function filter(callable $filter): static
    {
        return $this->clone(array_filter($this->classes, $filter, ARRAY_FILTER_USE_KEY));
    }

    public function clear(): static
    {
        return $this->clone([]);
    }
}
