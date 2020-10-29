<?php

namespace Whitecube\BemComponents;

use Illuminate\View\Component;

abstract class BemComponent extends Component
{
    public $modifiers;
    public $classes;

    public function __construct($modifiers = [], $classes = '')
    {
        $this->modifiers = $this->processModifiers($modifiers);
        $this->classes = $this->processClasses($classes);
    }

    /**
     * Generate the base bem class with modifiers
     * and additional classes
     *
     * @param string $base
     * @return string
     */
    public function bem($base)
    {
        $modifiers = array_map(function($modifier) use ($base) {
            return $base . '--' . $modifier;
        }, $this->getModifiers());

        return implode(' ', array_filter(array_merge([$base], $modifiers, $this->getClasses())));
    }

    /**
     * Get the additional classes
     *
     * @return array
     */
    public function getClasses()
    {
        if(is_null($this->classes)) {
            $this->classes = $this->processClasses($this->attributes->get('class'));
        }

        return $this->classes;
    }

    /**
     * Process the class string into an array
     *
     * @param string $classes
     * @return array
     */
    protected function processClasses($classes)
    {
        return explode(' ', $classes);
    }

    /**
     * Get the modifiers
     *
     * @return array
     */
    protected function getModifiers()
    {
        if(! is_null($this->attributes)) {
            $modifiers = $this->attributes->get('modifiers');

            $this->modifiers = array_merge(
                $this->processModifiers($modifiers),
                $this->modifiers ?? []
            );
        }

        return $this->modifiers ?? [];
    }

    /**
     * Process the given modifiers into an array
     *
     * @param $modifiers
     * @return array
     */
    protected function processModifiers($modifiers)
    {
        return is_array($modifiers) ? $modifiers : array_filter(explode(' ', $modifiers));
    }

    /**
     * Check if modifier is present
     *
     * @param string $modifier
     * @return bool
     */
    public function hasModifier($modifier)
    {
        return in_array($modifier, $this->getModifiers());
    }

    /**
     * Check if class exists
     *
     * @param string $classname
     * @return bool
     */
    public function hasClass($classname)
    {
        return in_array($classname, $this->getClasses());
    }

    /**
     * Set a modifier programmatically
     *
     * @param string $modifier
     * @return void
     */
    public function modifier(string $modifier)
    {
        if (is_null($this->modifiers)) {
            $this->modifiers = [];
        }

        $this->modifiers[] = $modifier;
    }

    /**
     * Set multiple modifiers programmatically
     *
     * @param array $modifiers
     * @param boolean $overwrite
     * @return void
     */
    public function modifiers(array $modifiers, $overwrite = false)
    {
        if ($overwrite) {
            $this->modifiers = $modifiers;

            return;
        }

        $this->modifiers = array_merge($this->getModifiers(), $modifiers);
    }

}
