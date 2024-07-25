<?php

declare(strict_types=1);

namespace Flying\Util\Css;

abstract class AbstractClasses implements ClassesInterface
{
    protected array $classes;

    public function __construct(mixed ...$classes)
    {
        if (count($classes) === 1 && $classes[0] instanceof self) {
            // Optimize a case when a new instance is created from single instance of Classes
            $this->classes = $classes[0]->classes;
        } else {
            $this->classes = $this->classesList($classes);
        }
    }

    public function has(string $class): bool
    {
        return array_key_exists($class, $this->classes);
    }

    public function count(): int
    {
        return count($this->classes);
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

    protected function classesList(iterable $classes): array
    {
        $result = [];
        foreach ($this->toTokenList($classes) as $class) {
            $result[trim($class)] = true;
        }
        return $result;
    }

    private function toTokenList(iterable $entries): iterable
    {
        foreach ($entries as $entry) {
            if (is_iterable($entry)) {
                yield from array_keys($this->classesList($entry));
            } elseif (is_string($entry)) {
                yield from preg_split('/\s+/', $entry);
            }
        }
    }
}
