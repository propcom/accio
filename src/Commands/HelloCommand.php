<?php
declare(strict_types=1);

namespace Propcom\Accio\Commands;

use Propcom\Accio\CliInput;
use Propcom\Accio\CliOutput;

/**
 * This should serve as an example of commands for getting started
 *
 * @package Propcom\Accio\Commands
 */
class HelloCommand
{

	/**
	 * A basic command that implements the usual hello world example
	 *
	 * @param CliInput $input
	 * @param CliOutput $output
	 */
	public function world(CliInput $input, CliOutput $output)
	{
		$output->write('Hello World!');
	}

	/**
	 * An advanced command that implements in improved hello world example
	 *
	 * Now you can use the option `--name` to provide a name for the command to
	 * say hello to.
	 *
	 * Options:
	 *
	 *   --name=<name>
	 *
	 * @param CliInput $input
	 * @param CliOutput $output
	 */
	public function name(CliInput $input, CliOutput $output)
	{
		$output->write('Hello ' . $this->getName($input));
	}

	private function getName(CliInput $input): string
	{
		return $input->getOptionValue('name', 'nameless person') ?: 'World';
	}
}
