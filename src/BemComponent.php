<?php

namespace Whitecube\BemComponents;

use Illuminate\View\Component;

abstract class BemComponent extends Component
{
    public $modifiers;
    public $classes;

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
            $this->classes = explode(' ', $this->attributes->get('class'));
        }

        return $this->classes;
    }

    /**
     * Get the modifiers
     *
     * @return array
     */
    protected function getModifiers()
    {
        if(is_null($this->modifiers)) {
            $modifiers = $this->attributes->get('modifiers');
            $this->modifiers = is_array($modifiers) ? $modifiers : explode(' ', $modifiers);
        }

        return $this->modifiers;
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

}
