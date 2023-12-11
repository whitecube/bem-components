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
        ComponentAttributeBag::macro('bem', fn(string $base, string|array $extraModifiers = []) => call_user_func($this->bemResolver, $base, $extraModifiers));
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
