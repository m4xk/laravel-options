<?php

use Appstract\Options\Option;

if (!function_exists('option')) {
    /**
     * Get / set the specified option value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param array|string $key
     * @param mixed $default
     * @param string $scope
     * @return mixed|\Appstract\Options\Option
     */
    function option($key = null, $default = null, $scope = Option::SCOPE_DEFAULT)
    {
        if (is_null($key)) {
            return app('option');
        }

        if (is_array($key)) {
            return app('option')->set($key, $scope = $scope);
        }

        return app('option')->get($key, $default);
    }
}

if (!function_exists('option_exists')) {
    /**
     * Check the specified option exits.
     *
     * @param string $key
     * @param string $scope
     * @return mixed
     */
    function option_exists($key, $scope = Option::SCOPE_DEFAULT)
    {
        return app('option')->exists($key);
    }
}
