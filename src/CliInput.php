<?php
declare(strict_types=1);

namespace Propcom\Accio;

use PHP_CodeSniffer\Tokenizers\PHP;

class CliInput
{

	/**
	 * @var string
	 */
	protected $script_name;

	/**
	 * @var string[]
	 */
	private $options = [];

	/**
	 * @var int[]
	 */
	private $flags = [];

	/**
	 * @var string[]
	 */
	private $arguments = [];

	public function __construct(array $argv)
	{
		$this->script_name = array_shift($argv);

		foreach ($argv as $item) {
			if (!is_string($item)) {
				throw new \UnexpectedValueException('Input items must be strings');
			} elseif ($item[0] !== '-') {
				// Arguments don't start with hyphens
				$this->arguments[] = $item;
			} elseif (strpos($item, '=') !== false) {
				// Value options have an equals
				list($key, $value) = explode('=', $item, 2);
				$key = ltrim($key, '-');
				$this->options[$key] = $value;
			} elseif ($item[1] === '-') {
				// Long flags have two hyphens
				$key = ltrim($item, '-');
				$this->setFlag($key);
			} else {
				// Short options can be compressed
				$flags = str_split(ltrim($item, '-'));
				foreach ($flags as $flag) {
					$this->setFlag($flag);
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function getScriptName(): string
	{
		return $this->script_name;
	}

	/**
	 * @return string
	 */
	public function getCommand(): string
	{
		return $this->command;
	}

	private function setFlag($name)
	{
		if (array_key_exists($name, $this->flags)) {
			$this->flags[$name]++;
		} else {
			$this->flags[$name] = 1;
		}
	}

	public function getFlag(string $long_name, string $short_name = ''): int
	{
		$count = 0;

		if (array_key_exists($long_name, $this->flags)) {
			$count += $this->flags[$long_name];
		}

		if ($short_name and array_key_exists($short_name, $this->flags)) {
			$count += $this->flags[$short_name];
		}

		if (array_key_exists($long_name, $this->options)) {
			throw new InvalidInputException('--' . $long_name . ' does not expect a value, but you provided one');
		}

		if (array_key_exists($short_name, $this->options)) {
			throw new InvalidInputException('-' . $short_name . ' does not expect a value, but you provided one');
		}

		return $count;
	}

	public function getOptionValue(string $name, string $default = ''): string
	{
		if (array_key_exists($name, $this->options)) {
			return $this->options[$name];
		}

		if (array_key_exists($name, $this->flags)) {
			return $default;
		}

		return '';
	}

	public function hasOption(string $name): bool
	{
		return array_key_exists($name, $this->options) || array_key_exists($name, $this->flags);
	}

	public function getArgument(int $position): string
	{
		if (count($this->arguments) < $position) {
			throw new MissingArgumentException('Too few arguments were passed, expected at least ' . $position . ' arguments');
		}

		return $this->arguments[$position];
	}

	public function shiftArgument(): string
	{
		$shifted = array_shift($this->arguments);

		if ($shifted === null) {
			throw new \UnderflowException('No arguments exist');
		}

		return $shifted;
	}

	public function getArguments(): array
	{
		return $this->arguments;
	}

	public function userInput(string $prompt): string
	{
		return readline($prompt);
	}

	public function userChoice(array $options, string $prompt = 'Please choose one of the above options by entering the corresponding number: '): int
	{
		if (array_values($options) !== $options) {
			throw new \InvalidArgumentException('Options array must be an indexed array');
		}

		$answer = 0;

		while (!$answer) {
			echo PHP_EOL;

			foreach (array_values($options) as $key => $option) {
				printf('[%d] %s' . PHP_EOL, $key + 1, $option);
			}

			echo PHP_EOL;

			$answer = $this->userInput($prompt);
			$answer = filter_var($answer, FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 1, 'max_range' => count($options)]]);

			if (!$answer) {
				echo "Invalid option" . PHP_EOL;
				$answer = 0;
			}
		}

		return $answer - 1;
	}
}