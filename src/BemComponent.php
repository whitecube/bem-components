<?php

namespace Whitecube\BemComponents;

use Illuminate\View\Component;

abstract class BemComponent extends Component
{
    use HasBemClasses;

    public function __construct($modifiers = [], $classes = '')
    {
        $this->modifiers($modifiers);
        $this->classes($classes);
    }
}
