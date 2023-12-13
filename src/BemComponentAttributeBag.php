<?php

namespace Whitecube\BemComponents;

use Closure;
use Illuminate\View\ComponentAttributeBag;

class BemComponentAttributeBag extends ComponentAttributeBag
{
    /**
     * The BEM macro callback function
     */
    protected Closure $bemResolver;

    /**
     * Define the BEM resolver callback.
     */
    public function setBemResolver(Closure $bemResolver): static
    {
        $this->bemResolver = $bemResolver;

        return $this;
    }

    /**
     * Resolve the BEM attributes and merge the resulting CSS classes 
     * in this bag.
     */
    public function resolveBemClasses(string $base, string|array $extraModifiers = []): static
    {
        call_user_func($this->bemResolver, $base, $extraModifiers);

        return $this;
    }

    /**
     * Only include the given attribute from the attribute array.
     */
    public function only($keys)
    {
        $instance = parent::only($keys);
        $instance->setBemResolver($this->bemResolver);

        return $instance;
    }

    /**
     * Exclude the given attribute from the attribute array.
     */
    public function except($keys)
    {
        $instance = parent::except($keys);
        $instance->setBemResolver($this->bemResolver);

        return $instance;
    }

    /**
     * Filter the attributes, returning a bag of attributes that pass the filter.
     */
    public function filter($callback)
    {
        $instance = parent::filter($callback);
        $instance->setBemResolver($this->bemResolver);

        return $instance;
    }

    /**
     * Merge additional attributes / values into the attribute bag.
     */
    public function merge(array $attributeDefaults = [], $escape = true)
    {
        $instance = parent::merge($attributeDefaults, $escape);
        $instance->setBemResolver($this->bemResolver);

        return $instance;
    }
}
