<?php

namespace Laradock;

use ArrayObject;
use Symfony\Component\Yaml\Yaml;

class Config implements \ArrayAccess
{

	/**
	 * Raw config data
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Config constructor.
	 *
	 * @param array $data
	 */
	public function __construct(array $data = [])
	{
		$this->data = $data;
	}

	/**
	 * Get list of port numbers mapped for service
	 *
	 * @param $service
	 *
	 * @return array
	 */
	public function getServicePorts($service)
	{
		$portsMap = $this->get("ports.{$service}", []);
		$ports = array_map(function ($map) {
			list($port) = explode(':', $map);

			return $port;
		}, $portsMap);

		return $ports;
	}

	public function get($key, $default = null)
	{
		return Arr::get($this->data, $key, $default);
	}

	/**
	 * Read config from Yaml file and create new instance
	 *
	 * @param $path
	 *
	 * @return \Laradock\Config
	 */
	public static function fromFile($path)
	{
		return new static(Yaml::parse(file_get_contents($path)));
	}

	/**
	 * Whether a offset exists
	 *
	 * @link https://php.net/manual/en/arrayaccess.offsetexists.php
	 *
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists($offset)
	{
		return isset($this->data[$offset]);
	}

	/**
	 * Offset to retrieve
	 *
	 * @link https://php.net/manual/en/arrayaccess.offsetget.php
	 *
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 *
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet($offset)
	{
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}

	/**
	 * Offset to set
	 *
	 * @link https://php.net/manual/en/arrayaccess.offsetset.php
	 *
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet($offset, $value)
	{
		$this->data[$offset] = $value;
	}

	/**
	 * Offset to unset
	 *
	 * @link https://php.net/manual/en/arrayaccess.offsetunset.php
	 *
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 *
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset($offset)
	{
		unset($this->data[$offset]);
	}
}
