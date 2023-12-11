<?php

namespace Whitecube\BemComponents;

use Illuminate\View\ComponentAttributeBag;

trait HasBemClasses
{
    /**
     * The component's BEM modifiers.
     */
    protected array $modifiers = [];

    /**
     * The component's additional classes.
     */
    protected array $classes = [];

    /**
     * Define the component's modifiers.
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
     * Add a single component modifier.
     */
    public function modifier(string $modifier): static
    {
        return $this->modifiers([$modifier]);
    }

    /**
     * Define the component's eventual additionnal classes.
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
     * Get and remove eventual existing attributes
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
     * Transform a class attribute into an usable array of classes.
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
     * Get the defined modifiers as an array of fully qualified classes.
     */
    protected function getModifierClasses(string $base): array
    {
        return $this->buildModifierClasses($base, $this->getModifiers());
    }

    /**
     * Get the defined modifiers as array.
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
     * Get the defined classes as array.
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
     * Check if a modifier is defined.
     */
    public function hasModifier(string $modifier): bool
    {
        return in_array($modifier, $this->getModifiers());
    }

    /**
     * Check if a class is defined.
     */
    public function hasClass(string $classname): bool
    {
        return in_array($classname, $this->getClasses());
    }

    /**
     * Generate the full class attribute, containing the BEM "base" and its modifier variants.
     */
    public function bem(string $base, string|array $modifiers = []): string
    {
        return implode(' ', $this->buildModifierClasses($base, $this->processClassList($modifiers)));
    }

    /**
     * Get all of the fully qualified classes for this component as an array.
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
     * Generate an array of all fully qualified BEM classes for provided base and modifiers.
     */
    protected function buildModifierClasses(string $base, array $modifiers): array
    {
        return array_values(array_filter(array_merge(
            [$base],
            array_map(fn($modifier) => $base.'--'.$modifier, $modifiers),
        )));
    }

    /**
     * Get the component's attribute bag with the merged BEM & additional classes.
     */
    protected function mergeBemClassesInAttributes(string $base, string|array $extraModifiers = []): ComponentAttributeBag
    {
        $this->modifiers($extraModifiers);

        $this->attributes['class'] = implode(' ', $this->getAllClasses($base));

        return $this->attributes;
    }

    /**
     * Get a new attribute bag instance.
     */
    protected function newAttributeBag(array $attributes = [])
    {
        $bag = parent::newAttributeBag($attributes);

        $bag->bemResolver = fn(string $base, string|array $extraModifiers = []) => $this->mergeBemClassesInAttributes($base, $extraModifiers);

        return $bag;
    }
}
