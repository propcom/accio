<?php
declare(strict_types=1);

namespace Accio;

class InputFactory
{
	/**
	 * @var InputFactory
	 */
	private static $instance;

	private function __construct()
	{
	}

	/**
	 * @return InputFactory
	 */
	public static function instance(): InputFactory
	{
		!static::$instance and static::$instance = new static();

		return static::$instance;
	}

	/**
	 * @return array
	 */
	public function getDefinedCommands(): array
	{
		return array_keys($this->input_command_map);
	}

	/**
	 * Returns an Input Object
	 * @param array $args
	 * @return CliInput
	 * @throws Exception
	 */
	public function createInput(array $args): CliInput
	{
		$command = array_shift($args);
		$args = $this->parseArgs($args);

		return new CliInput($args);
	}

	/**
	 * @param array $args
	 * @return array
	 */
	private function parseArgs(array $args): array
	{
		$args_array = [];

		foreach ($args as $input) {
			$input = explode('=', $input);
			$args_array[ltrim($input[0], '-')] = $input[1];
		}

		return $args_array;
	}
}