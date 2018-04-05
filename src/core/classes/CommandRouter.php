<?php
declare(strict_types=1);

class CommandRouter
{
	/**
	 * @var string
	 */
	private $command;

	/**
	 * @var string
	 */
	private $function;

	public function __construct(string $command)
	{
		$pieces = explode(':', $command);
		$this->command = ucfirst($pieces[0]);
		$this->function = (count($pieces) > 1) ? $pieces[1] : 'run';
	}

	/**
	 * @return string
	 */
	public function getCommand(): string
	{
		return $this->command;
	}

	/**
	 * @return string
	 */
	public function getFunction(): string
	{
		return $this->function;
	}

	/**
	 * @return bool
	 */
	public function isValidCommand(): bool
	{
		return (class_exists("{$this->command}Command") && is_subclass_of("{$this->command}Command", AbstractCommand::class));
	}

	/**
	 * @return bool
	 */
	public function isValidFunction(): bool
	{
		return $this->isValidCommand() && method_exists("{$this->command}Command", $this->function);
	}

	public function run($input)
	{
		$command_class = "{$this->command}Command";

		call_user_func([new $command_class(), $this->function], $input);
	}
}