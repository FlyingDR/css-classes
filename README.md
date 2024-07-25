# css-classes
Simple library to manage a list of CSS classes. 

It simplifies tasks of constructing a list of CSS classes from given information, adding and removing classes from the list.

## Installation

Simply install it using Composer:

```bash
composer require flying/css-classes
```

## Standalone usage

Functionality is exposed via [`Classes`](src/Classes.php) and [`MutableClasses`](src/MutableClasses.php) classes, both implements same [`ClassesInterface`](src/ClassesInterface.php) interface.

Further documentation uses immutable `Classes` implementation as an example unless explicitly defined otherwise, but `MutableClasses` have exactly same usage in except of mutability.

### Accepted values

Any method that is mean to accept CSS classes actually accepts arbitrary number of arguments of any type (`mixed ...$classes`).

Given arguments are processed in this way:

 - Iterables are expanded
 - Nested values are flattened
 - Any values besides non-empty strings are ignored

### Immutability

v1 provides only immutable implementation. Since v2 there are both immutable `Classes` and mutable `MutableClasses` implementations.

### List creation

Classes list can be constructed using either by creating new `Classes` instance or by using `Classes::from()` static method. Both methods are [accepted](#accepted-values) variable amount arguments of any type. 

Example:

```php
$classes = Classes::from('foo', 'bar baz', ['a', 'b', 'c']);
// Outputs: foo bar baz a b c
echo $classes;
$classes = $classes->without('baz');
// Outputs: foo bar a b c
echo $classes;
$classes = $classes->filter(fn(string $class): bool => strlen($class) > 1);
// Outputs: foo bar
echo $classes;
$classes = $classes->with('x', null, true, false, 42, 1.2345, ['y', ['z']]);
// Outputs: foo bar x y z
echo $classes;
// Outputs: 5
echo count($classes);
```

Fluent interface is also possible to use:

```php
$classes = Classes::from('foo', 'bar baz', ['a', 'b', 'c'])
    ->without('baz')
    ->filter(fn(string $class): bool => strlen($class) > 1)
    ->with('x', null, true, false, 42, 1.2345, ['y', ['z']]);
// Outputs: foo bar x y z
echo $classes;
// Outputs: 5
echo count($classes);
```

### Classes list modification

These methods can be used to modify the list of CSS classes:

 - `with(...$classes)` - Add given CSS classes to the list
 - `without(...$classes)` - Remove given CSS classes from the list
 - `filter(callable $filter)` - Filter CSS classes list using provided filter. `$filter` signature is `(string $class): bool`
 - `clear()` - Remove all CSS classes from the list. For [immutable](#immutability) `Classes` implementation it is equivalent of `new Classes()`.

### Classes list exporting

Classes list can be exported as a plain array using `toArray()` method.

Also, `ClassesInterface` interface extends `ArrayAggregate` and `Stringable` interfaces, so it is also possible to iterate over the list of CSS classes and cast its value into string.

### Other methods

`ClassesInterface` extends `Countable` interface, so number of available CSS classes can be calculated using `count()` PHP function or by calling `count()` method. 

It is also possible to check if given CSS class is available in the list by using `has(string $class)` method.

### Standalone `classes()` function

For simple cases when it is only needs to create CSS classes list as a string from available data it is also possible to use `\Flying\Util\Css\classes()` function.

`classes()` function is available since v1.1.0

## Usage in Twig

Library includes `\Flying\Util\Css\Twig\ClassesExtension` Twig extension that exposes `classes` function that provides same functionality as a [main class](#standalone-usage).

It can be used as a simple construction of the classes list:  

```twig
{%- set category = 'some-category' -%}
{%- set active = true -%}
{%- set current = false -%}
{# "main-class some-category is-active" will be generated #}
<div class="{{ classes('main-class', category, active ? 'is-active': null, current ? 'is-current' : null) }}"></div>
```

But it is also possible to use CSS classes list modification methods:

```twig
{%- set categories = {foo: 'Foo', bar: 'Bar'} -%}
{%- set current = 'foo' -%}
<ul class="categories-list">
    {%- set cl = classes('category') -%}
    {% for id, name in categories %}
        {# For "foo" category: "category ctg-foo current" will be generated #}
        {# For "bar" category: "category ctg-bar" will be generated #}
        <li class="{{ cl.with('ctg-' ~ id, current == id ? 'current' : null) }}">{{ name }}</li>
    {% endfor %}
</ul>
```

Refer to the [Twig documentation](https://twig.symfony.com/doc/3.x/api.html#using-extensions) on how to add the extension to the Twig. 

For Symfony, there is a [separate bundle](https://github.com/FlyingDR/css-classes-bundle) to simplify integration.

## License

Library is licensed under MIT license.
