<?php

/**
 * Get path to config file
 *
 * @return string
 */
function getConfigFile(): string
{
	return LARADOCK_ROOT . '/config.yml';
}

if (!function_exists('value')) {
	/**
	 * Return the default value of the given value.
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	function value($value)
	{
		return $value instanceof Closure ? $value() : $value;
	}
}
