# BEM View Components for Laravel

This package introduces a `BemComponent` class that you can use instead of the regular `Component` class, which gives you a few helper methods to aid you with your bem-style components.


## Installation

You can install the package via composer:

```bash
composer require whitecube/bem-components
```

## Usage

```php
<?php

namespace App\View\Components;

use Whitecube\BemComponents\BemComponent;

class Btn extends BemComponent
{
    public $href;
    public $icon;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($href, $icon = null)
    {
        $this->href = $href;
        $this->icon = $icon;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.btn');
    }

}

```

```blade
<a href="{{ $href }}" class="{{ $bem('btn') }}">
    @if($hasModifier('icon'))
        <span class="btn__icon" data-icon="{{ $icon }}"></span>
    @endif
    
    {{ $slot }}
</a>

```

Now you can pass modifiers as you please
```blade
<x-btn href="#" modifiers="error">Error</x-btn>
will result in 
<a href="#" class="btn btn--error">Error</a>

<x-btn href="#" modifiers="primary icon" icon="calendar">Calendar</x-btn>
will result in 
<a href="#" class="btn btn--primary btn--icon">
    <span class="btn__icon" data-icon="calendar"></span>
    Calendar
</a>
```

## Available methods

### `$bem($base): string`
Get the compiled bem classes with modifiers. The modifiers are specified using the `modifiers` prop on your component, which accepts either a string of space-separated modifiers, or an array.

For example, calling `$bem('btn')` on these:

```blade
<x-btn modifiers="primary error" />
<x-btn :modifiers="['primary', 'error']" />
```

Will output `btn btn--primary btn--error`

Any additional classes are also kept:

```blade
<x-btn modifiers="primary error" class="header__btn" />
```

Will output `header__btn btn btn--primary btn--error`

---

### `$hasModifier($modifier): bool`
Checks if the specified modifier is applied on this component.

---

### `$hasClass($class): bool`
Checks if the specified class is applied on this component.

## 💖 Sponsorships

If you are reliant on this package in your production applications, consider [sponsoring us](https://github.com/sponsors/whitecube)! It is the best way to help us keep doing what we love to do: making great open source software.

## Contributing

Feel free to suggest changes, ask for new features or fix bugs yourself. We're sure there are still a lot of improvements that could be made, and we would be very happy to merge useful pull requests.

Thanks!

## Made with ❤️ for open source

At [Whitecube](https://www.whitecube.be) we use a lot of open source software as part of our daily work.
So when we have an opportunity to give something back, we're super excited!

We hope you will enjoy this small contribution from us and would love to [hear from you](mailto:hello@whitecube.be) if you find it useful in your projects. Follow us on [Twitter](https://twitter.com/whitecube_be) for more updates!
