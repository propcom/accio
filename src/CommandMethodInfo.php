<?php
declare(strict_types=1);

namespace Propcom\Accio;

class CommandMethodInfo
{

	private $class;
	private $name;

	public function __construct(CommandClassInfo $class, string $method)
	{
		$this->class = $class;
		$this->name = $method;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getInvocation(): string
	{
		return $this->class->getName() . ':' . $this->getName();
	}

	public function getUsage(): string
	{
		$reflector = new \ReflectionClass($this->class->getFqClass());
		$docblock = $reflector->getMethod($this->getName())->getDocComment();

		if ($docblock === false) {
			return '';
		}

		$comment = substr($docblock, 3, strpos($docblock, '@') - 3);
		$comment = str_replace(['\r\n', '\n\r', '\r'], '\n', $comment);
		$comment = preg_replace('/(^|\n)\s*\* ?/', '$1', $comment);
		$comment = preg_replace('/\h+($|\n)/', '$1', $comment);

		preg_match('/^(.+)\v\v(.*)$/suU', $comment, $matches);
		$usage = str_replace("\n", ' ', $matches[1]);
		$details = str_replace(["\n\n", "\n", "\r"], ["\r", ' ', "\n"], $matches[2]);

		if ($details) {
			$usage .= "\n" . $details;
		}

		return $usage;
	}
}
