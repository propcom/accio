<?php
declare(strict_types=1);

class CliInput
{
	/**
	 * @var array
	 */
	private $data = [];

	public function __construct(array $input)
	{
		$this->data = $input;
	}

	public function __call($name, $args = [])
	{
		if (strpos($name, 'get') !== 0) {
			throw new \Exception("Uncaught Error: Call to undefined method CliInput::{$name}()");
		}

		$key = strtolower(str_replace('get', '', $name));
		if (!array_key_exists($key, $this->data)) {
			throw new \Exception("No {$key} entered");
		}

		return $this->data[$key];
	}
}