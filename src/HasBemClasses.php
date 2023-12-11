<?php

namespace Whitecube\BemComponents;

use Illuminate\View\ComponentAttributeBag;

trait HasBemClasses
{
    /**
     * The modifiers that should be applied on the component's "base" class.
     */
    protected array $modifiers;

    /**
     * The additionnal classes that should be merged into the component's "class" attribute.
     */
    protected array $classes;

    /**
     * Define the component's modifiers.
     */
    public function modifiers(string|array $modifiers): static
    {
        $this->modifiers = $this->processClasses($modifiers);

        return $this;
    }

    /**
     * Add a single component modifier.
     */
    public function modifier(string $modifier): static
    {
        $this->modifiers = array_merge(
            $this->modifiers ?: [],
            $this->processClasses($modifier),
        );

        return $this;
    }

    /**
     * Define the component's eventual additionnal classes.
     */
    public function classes(string|array $classes): static
    {
        $this->classes = $this->processClasses($classes);

        return $this;
    }

    /**
     * Transform a class attribute into an usable array of classes.
     */
    protected function processClasses(string|array $items): array
    {
        if (is_string($items)) {
            $items = explode(' ', $items);
        }

        return array_values(array_filter($items));
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
        return array_filter(array_merge(
            ($this->attributes ? $this->processClasses($this->attributes->get('modifier')) : null) ?: [],
            ($this->attributes ? $this->processClasses($this->attributes->get('modifiers')) : null) ?: [],
            $this->modifiers ?: [],
        ));
    }

    /**
     * Get the defined classes as array.
     */
    protected function getClasses(): array
    {
        return array_filter(array_merge(
            ($this->attributes ? $this->processClasses($this->attributes->get('class')) : null) ?: [],
            $this->classes ?: [],
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
    public function attributesWithBem(string $base): ComponentAttributeBag
    {
        $this->attributes = $this->attributes ?: $this->newAttributeBag();

        $this->attributes->class($this->getAllClasses($base));

        return $this->attributes;
    }
}
