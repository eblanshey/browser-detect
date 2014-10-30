<?php
namespace hisorange\BrowserDetect;

use ArrayIterator, ArrayAccess, JsonSerializable;

class Result implements ArrayAccess, JsonSerializable {

	/**
	 * All of the attributes set on the container.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Separator character for compact strings.
	 *
	 * @var string
	 */
	const SEPARATOR = '|';

	/**
	 * @param  array|object $attributes
	 */
	public function __construct($attributes = array())
	{
		foreach ($attributes as $key => $value)
		{
			$this->attributes[$key] = $value;
		}
	}

	/**
	 * Import attributes from array or string.
	 *
	 * @param  array|string $raw
	 * @return self
	 */
	public function import($raw)
	{
		return is_array($raw) ? $this->importFromArray($raw) : $this->importFromString($raw);
	}

	/**
	 * Import a result from a compact string format to the object.
	 * Split and merge with the schema. Also convert the is* values back to boolean.
	 *
	 * @param  string $raw
	 * @return self
	 */
	public function importFromString($raw)
	{
		$this->attributes = $this->fixTypes(array_combine(array_keys(Parser::getEmptyDataSchema()), explode(self::SEPARATOR, $raw)));
		return $this;
	}

	/**
	 * Import a result from an array to the object.
	 * Sniff out if the array has named keys or need to merge with the schema.
	 *
	 * @param  array $raw
	 * @return self
	 */
	public function importFromArray(array $raw)
	{
		// Load the schema keys for validation.
		$schema 			= array_keys(Parser::getEmptyDataSchema());

		// If the imported array has numeric keys then combine the values.
		$this->attributes = $this->fixTypes(($schema != array_keys($raw)) ? array_combine($schema, $raw) : $raw);

		return $this;
	}

	/**
	 * Change the information's value types to the schema's value types.
	 *
	 * @param  array $attributes
	 * @return array
	 */
	protected function fixTypes($attributes)
	{
		// Load the schema keys for conversion.
		$schema 			= Parser::getEmptyDataSchema();

		foreach ($attributes as $key => &$value) {
			settype($value, gettype($schema[$key]));
		}

		return $attributes;
	}

	/**
	 * Export attributes to compact string format.
	 *
	 * @return boolean
	 */
	public function toString()
	{
		return implode(self::SEPARATOR, array_values(array_map(function($value) {
			return empty($value) ? '' : $value;
		}, $this->attributes)));
	}

	/**
	 * Export attributes to compact string format.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * Support for foreach.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->attributes);
	}

	/**
	 * Build a human readable browser name: Internet Explorer 7, Firefox 3.6
	 *
	 * @return string
	 */
	public function browserName()
	{
		return trim($this->attributes['browserFamily'].' '.$this->browserVersion());
	}

	/**
	 * Build human readable browser version. (cuts the trailing .0 parts)
	 *
	 * @return string
	 */
	public function browserVersion()
	{
		return $this->clearSemver($this->attributes['browserVersionMajor'].'.'.$this->attributes['browserVersionMinor'].'.'.$this->attributes['browserVersionPatch']);
	}

	/**
	 * Build a human readable os name: Windows 7, Windows XP, Android OS 2.3.6
	 *
	 * @return string
	 */
	public function osName()
	{
		return trim($this->attributes['osFamily'].' '.$this->osVersion());
	}

	/**
	 * Build human readable os version. (cuts the trailing .0 parts)
	 *
	 * @return string
	 */
	public function osVersion()
	{
		return $this->clearSemver($this->attributes['osVersionMajor'].'.'.$this->attributes['osVersionMinor'].'.'.$this->attributes['osVersionPatch']);
	}

	/**
	 * Is this browser an Internet Explorer?
	 *
	 * @return boolean
	 */
	public function isIE()
	{
		return preg_match('%(^IE$|internet\s+explorer)%i', $this->attributes['browserFamily']);
	}

	/**
	 * Is this an Internet Explorer X (or lower version).
	 *
	 * @return boolean
	 */
	public function isIEVersion($version, $lowerToo = false)
	{
		// Browser version cannot be higher, browser version cannot be lower only if the lowerToo is true, browser name need to be IE or Internet Explorer.
		return ! (($this->attributes['browserVersionMajor'] > $version) or ( ! $lowerToo and $this->attributes['browserVersionMajor'] < $version) or ! $this->isIE());
	}

	/**
	 * @since 1.1.0 clears semver.
	 *
	 * @return string
	 */
	protected function clearSemver($version)
	{
		return preg_replace('%(^0.0.0$|\.0\.0$|\.0$)%', '', $version);
	}

	/**
	 * Get an attribute from the container.
	 *
	 * @param  string  $key
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		if (array_key_exists($key, $this->attributes))
		{
			return $this->attributes[$key];
		}

		return value($default);
	}

	/**
	 * Get the attributes from the container.
	 *
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * Convert the Fluent instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->attributes;
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize()
	{
		return $this->toArray();
	}

	/**
	 * Convert the Fluent instance to JSON.
	 *
	 * @param  int  $options
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}

	/**
	 * Determine if the given offset exists.
	 *
	 * @param  string  $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->{$offset});
	}

	/**
	 * Get the value for a given offset.
	 *
	 * @param  string  $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->{$offset};
	}

	/**
	 * Set the value at the given offset.
	 *
	 * @param  string  $offset
	 * @param  mixed   $value
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->{$offset} = $value;
	}

	/**
	 * Unset the value at the given offset.
	 *
	 * @param  string  $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->{$offset});
	}

	/**
	 * Handle dynamic calls to the container to set attributes.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return $this
	 */
	public function __call($method, $parameters)
	{
		$this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;

		return $this;
	}

	/**
	 * Dynamically retrieve the value of an attribute.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->get($key);
	}

	/**
	 * Dynamically set the value of an attribute.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->attributes[$key] = $value;
	}

	/**
	 * Dynamically check if an attribute is set.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __isset($key)
	{
		return isset($this->attributes[$key]);
	}

	/**
	 * Dynamically unset an attribute.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __unset($key)
	{
		unset($this->attributes[$key]);
	}

}