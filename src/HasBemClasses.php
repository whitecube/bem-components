<?php

namespace Whitecube\BemComponents;

use Illuminate\View\ComponentAttributeBag;

trait HasBemClasses
{
    /**
     * The component's BEM CSS modifiers.
     */
    protected array $modifiers = [];

    /**
     * The component's additional CSS classes.
     */
    protected array $classes = [];

    /**
     * Merge multiple additional modifiers into the component's existing modifiers stack.
     */
    public function modifiers(string|array $modifiers): static
    {
        $this->modifiers = array_merge(
            $this->modifiers,
            $this->processClassList($modifiers),
        );

        return $this;
    }

    /**
     * Merge a single additional modifier into the component's existing modifiers stack.
     */
    public function modifier(string $modifier): static
    {
        return $this->modifiers([$modifier]);
    }

    /**
     * Merge multiple additional classnames into the component's existing CSS classes stack.
     */
    public function classes(string|array $classes): static
    {
        $this->classes = array_merge(
            $this->classes,
            $this->processClassList($classes),
        );

        return $this;
    }

    /**
     * Get and remove eventual existing keys defined in the component's attributes bag.
     */
    protected function pullClassListAttribute(string $key): array
    {
        $classList = $this->processClassList(
            $this->attributes->get($key, [])
        );

        unset($this->attributes[$key]);

        return $classList;
    }

    /**
     * Transform a list of raw classnames into an usable/cleaned array of CSS classes.
     */
    protected function processClassList(string|array $items): array
    {
        $cleaned = [];

        if (is_string($items)) {
            $items = explode(' ', $items);
        }

        foreach ($items as $value) {
            if(is_array($value)) $cleaned = array_merge($cleaned, $this->processClassList($value));
            else $cleaned[] = preg_replace('/[^A-Za-z0-9\-\_]/', '', strval($value));
        }

        return array_values(array_filter($cleaned));
    }

    /**
     * Get the defined modifiers as an array of fully qualified BEM CSS classes.
     */
    protected function getModifierClasses(string $base): array
    {
        return $this->buildModifierClasses($base, $this->getModifiers());
    }

    /**
     * Get all the accessible & defined modifiers as an array.
     */
    protected function getModifiers(): array
    {
        if(! $this->attributes || (! $this->attributes->has('modifier') && ! $this->attributes->has('modifiers'))) {
            return $this->modifiers;
        }

        return $this->modifiers = array_values(array_filter(array_merge(
            $this->modifiers,
            $this->pullClassListAttribute('modifier'),
            $this->pullClassListAttribute('modifiers'),
        )));
    }

    /**
     * Get the accessible & defined CSS classes as an array.
     */
    protected function getClasses(): array
    {
        if(! $this->attributes || ! $this->attributes->has('class')) {
            return $this->classes;
        }

        return $this->classes = array_values(array_filter(array_merge(
            $this->classes,
            $this->pullClassListAttribute('class'),
        )));
    }

    /**
     * Check if the specified modifier is applied on this component.
     */
    public function hasModifier(string $modifier): bool
    {
        return in_array($modifier, $this->getModifiers());
    }

    /**
     * Check if the specified CSS class is applied on this component.
     */
    public function hasClass(string $classname): bool
    {
        return in_array($classname, $this->getClasses());
    }

    /**
     * Generate a "class" attribute's value based on a BEM "base" and its eventual modifiers.
     */
    public function bem(string $base, string|array $modifiers = []): string
    {
        return implode(' ', $this->buildModifierClasses($base, $this->processClassList($modifiers)));
    }

    /**
     * Get all of the defined & fully qualified CSS classes for this component as an array.
     */
    protected function getAllClasses(string $base): array
    {
        $classes = array_unique(array_filter(array_merge(
            $this->getModifierClasses($base),
            $this->getClasses(),
        )));

        asort($classes);

        return array_values($classes);
    }

    /**
     * Generate an array fully qualified CSS BEM classes based on the provided base classname and modifiers.
     */
    protected function buildModifierClasses(string $base, array $modifiers): array
    {
        return array_values(array_filter(array_merge(
            [$base],
            array_map(fn($modifier) => $base.'--'.$modifier, $modifiers),
        )));
    }

    /**
     * Apply all the component's defined CSS classes and BEM modifiers into the component's "class" attribute.
     */
    protected function mergeAllClassesInAttributeBag(string $base, string|array $extraModifiers = []): ComponentAttributeBag
    {
        $this->modifiers($extraModifiers);

        $this->attributes['class'] = implode(' ', $this->getAllClasses($base));

        return $this->attributes;
    }

    /**
     * Get a new attribute bag instance with its BEM resolver callback.
     */
    protected function newAttributeBag(array $attributes = [])
    {
        $bag = parent::newAttributeBag($attributes);

        $bag->bemResolver = fn(string $base, string|array $extraModifiers = []) => $this->mergeAllClassesInAttributeBag($base, $extraModifiers);

        return $bag;
    }
}
