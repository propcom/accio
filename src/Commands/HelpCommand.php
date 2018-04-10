<?php
declare(strict_types=1);

namespace Propcom\Accio\Commands;

use Propcom\Accio\CliFormat;
use Propcom\Accio\CliInput;
use Propcom\Accio\CliOutput;
use Propcom\Accio\CommandRouter;

class HelpCommand
{

	/**
	 * @var CommandRouter
	 */
	private $command_router;

	public function __construct(CommandRouter $command_router)
	{
		$this->command_router = $command_router;
	}

	/**
	 * Display all of the available commands
	 *
	 * @param CliInput $input
	 * @param CliOutput $output
	 *
	 * @return int
	 */
	public function showCommands(CliInput $input, CliOutput $output): int
	{
		$output->newLine();

		$output->write('Below is the list of available commands:');

		$output->newLine();

		foreach ($this->command_router->getAllCommands() as $class_info) {
			if ($class_usage = $class_info->getUsage()) {
				$output->write($output->indent($class_usage));
			}

			foreach ($class_info->getMethods() as $method_info) {
				//$usage = $this->getUsage($command_name, $command_method);
				$output->highlight('	' . $method_info->getInvocation());

				if ($method_usage = $method_info->getUsage()) {
					$output->write($output->indent($method_usage, "\t\t"));
				}
			}

			$output->newLine();
		}

		return 0;
	}

	private function getUsage(string $class_name, string $method)
	{
		$reflector = new ReflectionClass($class_name);
		$docblock = $reflector->getMethod($method)->getDocComment();

		if ($docblock !== false) {
			$docblock = explode("@", $docblock)[0];
			$docblock = ltrim(rtrim($docblock, '*/'), "/**");
			$docblock = trim(str_replace('*', '', $docblock));
		}

		return $docblock;
	}
}