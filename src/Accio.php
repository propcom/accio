<?php

namespace Propcom\Accio;


class Accio
{

	/**
	 * @var CommandRouter
	 */
	private $command_router;

	public function __construct(CommandRouter $command_router)
	{
		$this->command_router = $command_router;
	}

	public function run(CliInput $input, CliOutput $output = null)
	{
		$command = $this->command_router->route($input);
		$result = $this->command_router->execute($command, $input, $output);
	}
}