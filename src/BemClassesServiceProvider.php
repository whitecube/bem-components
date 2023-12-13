<?php

namespace Whitecube\BemComponents;

use Illuminate\View\ComponentAttributeBag;
use Illuminate\Support\ServiceProvider;

class BemClassesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        ComponentAttributeBag::macro(
            'bem',
            function(string $base, string|array $extraModifiers = []) {
                return $this->resolveBemClasses($base, $extraModifiers);
            }
        );
    }

    /**
     * Register bindings
     *
     * @return void
     */
    public function register()
    {
    }
}
