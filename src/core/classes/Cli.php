<?php
declare(strict_types=1);

class Cli
{
	/**
	 * @param string $message
	 * @return string
	 */
	public function getInput($message = '')
	{
		return readline($message);
	}

	/**
	 * @param string $output
	 * @param int $color
	 */
	public function write(string $output, int $color = 37)
	{
		echo sprintf("\033[%sm%s\033[0m\n", $color, $output);
	}

	/**
	 * @param string $error_message
	 */
	public function error(string $error_message)
	{
		$this->write($error_message, CliColor::RED);
	}
}