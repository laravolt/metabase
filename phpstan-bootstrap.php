<?php

/**
 * PHPStan bootstrap file for Laravel package
 * This file defines Laravel helper functions for static analysis
 */

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string|null $abstract
     * @param array $parameters
     * @return mixed|\Illuminate\Contracts\Foundation\Application
     */
    function app($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return \Illuminate\Container\Container::getInstance();
        }

        return \Illuminate\Container\Container::getInstance()->make($abstract, $parameters);
    }
}

if (!function_exists('config')) {
    /**
     * Get / set the specified configuration value.
     *
     * @param array|string|null $key
     * @param mixed $default
     * @return mixed|\Illuminate\Config\Repository
     */
    function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return new \Illuminate\Config\Repository();
        }

        if (is_array($key)) {
            return new \Illuminate\Config\Repository();
        }

        return $default;
    }
}

if (!function_exists('view')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string|null $view
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    function view($view = null, $data = [], $mergeData = [])
    {
        $factory = new class implements \Illuminate\Contracts\View\Factory {
            public function exists($view) { return true; }
            public function file($path, $data = [], $mergeData = []) { return $this->make('', $data, $mergeData); }
            public function make($view, $data = [], $mergeData = []) {
                return new class implements \Illuminate\Contracts\View\View {
                    public function name() { return ''; }
                    public function with($key, $value = null) { return $this; }
                    public function getData() { return []; }
                    public function render() { return ''; }
                };
            }
            public function composer($views, $callback) { return []; }
            public function creator($views, $callback) { return []; }
            public function addNamespace($namespace, $hints) { return $this; }
            public function replaceNamespace($namespace, $hints) { return $this; }
        };

        if (is_null($view)) {
            return $factory;
        }

        return $factory->make($view, $data, $mergeData);
    }
}

if (!function_exists('resource_path')) {
    /**
     * Get the path to the resources folder.
     *
     * @param string $path
     * @return string
     */
    function resource_path($path = '')
    {
        return '/resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (!function_exists('config_path')) {
    /**
     * Get the configuration path.
     *
     * @param string $path
     * @return string
     */
    function config_path($path = '')
    {
        return '/config' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}