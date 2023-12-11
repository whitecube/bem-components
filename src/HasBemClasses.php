<?php

namespace Whitecube\BemComponents;

use Illuminate\View\ComponentAttributeBag;

trait HasBemClasses
{
    /**
     * Define the component's modifiers.
     */
    public function modifiers(string|array $modifiers): static
    {
        $this->attributes = $this->attributes ?: $this->newAttributeBag();

        $defined = $this->attributes->get('modifiers', []);

        $this->attributes['modifiers'] = array_merge(
            $this->processClassList($defined),
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
        $this->attributes = $this->attributes ?: $this->newAttributeBag();

        $defined = $this->attributes->get('class', []);

        $this->attributes['class'] = array_merge(
            $this->processClassList($defined),
            $this->processClassList($classes),
        );

        return $this;
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
        return array_map(fn($modifier) => $base.'--'.$modifier, $this->getModifiers());
    }

    /**
     * Get the defined modifiers as array.
     */
    protected function getModifiers(): array
    {
        return array_values(array_filter(array_merge(
            ($this->attributes ? $this->processClassList($this->attributes->get('modifier') ?: []) : null) ?: [],
            ($this->attributes ? $this->processClassList($this->attributes->get('modifiers') ?: []) : null) ?: [],
        )));
    }

    /**
     * Get the defined classes as array.
     */
    protected function getClasses(): array
    {
        return array_values(array_filter(
            ($this->attributes ? $this->processClassList($this->attributes->get('class') ?: []) : null) ?: []
        ));
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
     * Get all of the fully qualified classes for this component as an array.
     */
    protected function getAllClasses(string $base): array
    {
        $classes = array_unique(array_filter(array_merge(
            [$base],
            $this->getModifierClasses($base),
            $this->getClasses(),
        )));

        asort($classes);

        return array_values($classes);
    }

    /**
     * Generate the full class attribute, containing the BEM "base", its modifiers and eventual additional classes.
     */
    public function bem(string $base): string
    {
        return implode(' ', $this->getAllClasses($base));
    }

    /**
     * Get the component's attribute bag with the merged BEM & additional classes.
     */
    protected function mergeBemClassesInAttributes(string $base): ComponentAttributeBag
    {
        $this->attributes = $this->attributes ?: $this->newAttributeBag();

        $this->attributes['class'] = $this->bem($base);
        unset($this->attributes['modifier']);
        unset($this->attributes['modifiers']);

        return $this->attributes;
    }

    /**
     * Get a new attribute bag instance.
     */
    protected function newAttributeBag(array $attributes = [])
    {
        $bag = parent::newAttributeBag($attributes);

        $bag->bemResolver = fn(string $base) => $this->mergeBemClassesInAttributes($base);

        return $bag;
    }
}
