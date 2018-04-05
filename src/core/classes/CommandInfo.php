<?php
declare(strict_types=1);

class CommandInfo
{
	public function getDefinedCommands(): array
	{
		$commands = [];

		foreach (glob(CLI_ROOT.'/classes/commands/*.php') as $file) {
			$class_name = basename($file, '.php');
			$command_name = strtolower(str_replace('Command', '', $class_name));

			foreach ($this->getCommandMethods($class_name) as $method) {
				$command = "{$command_name}:{$method}";
				if ($usage = $this->getUsage($class_name, $method)) {
					$command .= " - ".$usage;
				}

				$commands[] = $command;
			}
		}

		return $commands;
	}

	private function getCommandMethods(string $class_name): Generator
	{
		foreach (get_class_methods($class_name) as $method) {
			if (!in_array($method, ['__construct'])) {
				yield $method;
			}
		}
	}

	public function getUsage(string $class_name, string $method)
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