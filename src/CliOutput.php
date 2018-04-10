<?php
declare(strict_types=1);

namespace Propcom\Accio;

class CliOutput
{
	/**
	 * @var resource
	 */
	private $stdout;

	/**
	 * @var resource
	 */
	private $stderr;

	public function __construct($output_stream = STDOUT, $error_stream = STDERR)
	{
		if (!is_resource($output_stream)) {
			throw new \InvalidArgumentException(
				sprintf(
					'Argument 1 [$output_stream] passed to %s must be of the type resource, %s given.',
					__METHOD__,
					gettype($output_stream)
				)
			);
		}

		if (!is_resource($error_stream)) {
			throw new \InvalidArgumentException(
				sprintf(
					'$Argument 1 [$error_stream] passed to %s must be of the type resource, %s given.',
					__METHOD__,
					gettype($error_stream)
				)
			);
		}

		$this->stdout = $output_stream;
		$this->stderr = $error_stream;
	}

	public function indent(string $string, string $indentation = "\t"): string
	{
		return $indentation . str_replace("\n", "\n" . $indentation, $string);
	}

	public function newLine(int $count = 1): void
	{
		$count = max($count, 1);
		$this->write(str_repeat("\n", $count - 1));
	}

	public function write(string $message, int ...$formats): void
	{
		$format = implode(';', $formats);
		fwrite($this->stdout, sprintf("\033[%sm%s\033[0m\n", $format, $message));
	}

	public function writeError(string $output, int ...$formats): void
	{
		$format = implode(';', $formats);
		fwrite($this->stderr, sprintf("\033[%sm%s\033[0m\n", $format, $output));
	}

	public function success(string $message): void
	{
		$this->write($message, CliFormat::FOREGROUND_GREEN);
	}

	public function highlight(string $message): void
	{
		$this->write($message, CliFormat::FOREGROUND_YELLOW);
	}

	public function highlight2(string $message): void
	{
		$this->write($message, CliFormat::FOREGROUND_CYAN);
	}

	public function error(string $message): void
	{
		$this->writeError($message, CliFormat::FOREGROUND_RED);
	}

	public function warning(string $message): void
	{
		$this->writeError($message, CliFormat::FOREGROUND_YELLOW);
	}

	public function notice(string $message): void
	{
		$this->writeError($message, CliFormat::FOREGROUND_CYAN);
	}

	public function info(string $message): void
	{
		$this->writeError($message, CliFormat::FOREGROUND_LIGHT_GREY);
	}

	public function debug(string $message): void
	{
		$this->writeError($message, CliFormat::FOREGROUND_DARK_GREY);
	}
}