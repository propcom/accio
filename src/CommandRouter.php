<?php
declare(strict_types=1);

namespace Propcom\Accio;

class CommandRouter
{

	private $command_directories = [__DIR__.'/Commands/' => 'Propcom\Accio\Commands'];

	public function __construct(array $command_directories)
	{
		foreach ($command_directories as $command_directory => $command_namespace) {
			$this->addCommandDirectory($command_directory, $command_namespace);
		}
	}

	public function addCommandDirectory(string $command_directory, string $command_namespace = ''): self
	{
		$command_directory = rtrim($command_directory, '/') . '/';
		$command_namespace = rtrim($command_namespace, '\\') . '\\';
		$this->command_directories[$command_directory] = $command_namespace;

		return $this;
	}

	public function route(CliInput $input): callable
	{
		try {
			$command = $input->shiftArgument();
		} catch (\UnderflowException $e) {
			$command = 'help:showCommands';
		}

		$parts = array_pad(explode(':', $command, 2), 2, 'run');

		if (!$command_class = $this->findCommandClass($parts[0])) {
			throw new \RuntimeException('Could not find command class');
		}

		if (!$command_class->hasMethod($parts[1])) {
			throw new \RuntimeException('Invalid command');
		}

		return [$command_class->getFqClass(), $parts[1]];
	}

	/**
	 * Execute a command callable (usually an array of fully qualified command
	 * class name and command method), with specific input and output instnaces
	 *
	 * @param callable $command
	 * @param CliInput $input
	 * @param CliOutput $output
	 *
	 * @return int Command exit code
	 */
	public function execute(callable $command, CliInput $input, CliOutput $output): int
	{
		try {
			$command[0] = new $command[0]($this);
			$exit_code = call_user_func($command, $input, $output);
		} catch (\Exception $e) {
			$output->error(sprintf('Uncaught Exception [%s] - %s', get_class($e), $e->getMessage()));
			return 128 + $e->getCode();
		}

		return intval($exit_code);
	}

	/**
	 * @return CommandClassInfo[]
	 */
	public function getAllCommands(): array
	{
		$commands = [];

		foreach ($this->command_directories as $command_directory => $command_namespace) {
			foreach (glob($command_directory . '*Command.php') as $command_filename) {
				$commands[] = CommandClassInfo::fromFile($command_filename, $command_namespace);
			}
		}

		return $commands;
	}

	/**
	 * Find and load a command class from within one of the command directories
	 * and return the fully qualified command class name
	 *
	 * @throws \RuntimeException when a file for the command class could not be founds
	 *
	 * @param string $command_name Name of the command to find and load the class for
	 *
	 * @return CommandClassInfo
	 */
	private function findCommandClass(string $command_name): CommandClassInfo
	{
		$command_name = ucfirst($command_name);

		foreach ($this->command_directories as $command_directory => $command_namespace) {
			$command_filename = $command_directory . $command_name . 'Command.php';

			if (is_file($command_filename)) {
				return CommandClassInfo::fromFile($command_filename, $command_namespace);
			}
		}

		throw new \RuntimeException('Could not load command class');
	}
}
