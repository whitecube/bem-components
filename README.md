# Easy BEM CSS classes for Laravel Components

This package introduces a `HasBemClasses` trait that you can use in Laravel's `App\View\Components` instances, providing a few useful helper methods and automations for a seamless BEM-style integration in your workflow.

## Installation

You can install the package via composer:

```bash
composer require whitecube/bem-components
```

## Usage

Generate your component files as you are used to, then add the `Whitecube\BemComponents\HasBemClasses` trait to the component's view controller in `App\View\Components`:

```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Whitecube\BemComponents\HasBemClasses;

class Btn extends Component
{
    use HasBemClasses;

    public ?string $icon;

    public function __construct(?string $icon = null)
    {
        $this->icon = $icon;

        if($this->icon) {
            $this->modifier('icon');
        }
    }

    public function render()
    {
        return view('components.btn');
    }
}
```

Then, don't forget to echo `$attributes->bem(string $base, string|array $extraModifiers = [])` inside your component's view:

```blade
<a {{ $attributes->bem('btn') }}>
    @if($hasModifier('icon'))
        <span class="btn__icon" data-icon="{{ $icon }}"></span>
    @endif
    <span class="btn__label">{{ $slot }}</span>
</a>
```

You can now pass modifiers and classes as you please:

```blade
<x-btn href="#" modifier="big">Click me!</x-btn>
<x-btn href="#" modifier="big" :modifiers="['foo','bar',null]">Click me!</x-btn>
<x-btn href="#" icon="eye">Click me!</x-btn>
<x-btn href="#" icon="eye" modifier="big">Click me!</x-btn>
<x-btn href="#" icon="eye" modifier="big" class="ajax">Click me!</x-btn>
```
```html
<a href="#" class="btn btn--big">
    <span class="btn__label">Click me!</span>
</a>
<a href="#" class="btn btn--bar btn--big btn--foo">
    <span class="btn__label">Click me!</span>
</a>
<a href="#" class="btn btn--icon">
    <span class="btn__icon" data-icon="eye"></span>
    <span class="btn__label">Click me!</span>
</a>
<a href="#" class="btn btn--big btn--icon">
    <span class="btn__icon" data-icon="eye"></span>
    <span class="btn__label">Click me!</span>
</a>
<a href="#" class="ajax btn btn--big btn--icon">
    <span class="btn__icon" data-icon="eye"></span>
    <span class="btn__label">Click me!</span>
</a>
```

## Available methods

#### `$bem(string $base, string|array $modifiers = []): string`

Get compiled BEM classes with modifiers. The `modifiers` parameter can either be a string of space-separated modifiers or an array.

For example, calling:
```php
$bem('btn__label', 'blue bold')
// OR
$bem('btn__label', ['blue','bold'])
```
will result in:
```
btn__label btn__label--blue btn__label--bold
```

---

#### `$hasModifier(string $modifier): bool`

Checks if the specified `modifier` is applied on this component.

> [!IMPORTANT]
> This method is also available inside the `Component` instance (using `$this->hasModifier(string $modifier)`) but unfortunately it is not able to check for modifiers defined as attributes on the component's tag (`<x-component modifiers="foo bar" />`) as of Laravel 10.x, because these values are not exposed before rendering the component's view. Let's hope this restriction will be lifted in the future.

If you need to access these modifiers inside the `Component` instance, you can always request them from the component's `__construct` parameters and inject them manually:

```php
public function __construct(null|string|array $modifiers = [])
{
    $this->modifiers($modifiers);

    // Now this will work:
    if($this->hasModifier('big')) {
        // ...
    }
}
```

---

#### `$hasClass(string $classname): bool`

Checks if the specified CSS classname is applied on this component.

> [!IMPORTANT]
> This method is also available inside the `Component` instance (using `$this->hasClass(string $classname)`) but unfortunately it is not able to check for classnames defined as attributes on the component's tag (`<x-component class="foo bar" />`) as of Laravel 10.x, because these values are not exposed before rendering the component's view. Let's hope this restriction will be lifted in the future.

If you need to access these classnames inside the `Component` instance, you can always request them from the component's `__construct` parameters and inject them manually:

```php
public function __construct(null|string|array $classnames = [])
{
    $this->classes($classnames);

    // Now this will work:
    if($this->hasClass('ajax')) {
        // ...
    }
}
```

## 💖 Sponsorships

If you are reliant on this package in your production applications, consider [sponsoring us](https://github.com/sponsors/whitecube)! It is the best way to help us keep doing what we love to do: making great open source software.

## Contributing

Feel free to suggest changes, ask for new features or fix bugs yourself. We're sure there are still a lot of improvements that could be made, and we would be very happy to merge useful pull requests.

Thanks!

## Made with ❤️ for open source

At [Whitecube](https://www.whitecube.be) we use a lot of open source software as part of our daily work.
So when we have an opportunity to give something back, we're super excited!

We hope you will enjoy this small contribution from us and would love to [hear from you](mailto:hello@whitecube.be) if you find it useful in your projects. Follow us on [Twitter](https://twitter.com/whitecube_be) for more updates!
