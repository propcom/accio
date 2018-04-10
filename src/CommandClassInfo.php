<?php
declare(strict_types=1);

namespace Propcom\Accio;

class CommandClassInfo
{
	private $name;
	private $class;
	private $namespace;
	private $filepath;
	private $methods = [];

	private function __construct(string $namespace)
	{
		$this->namespace = '\\' . trim($namespace, '\\') . '\\';
	}

	public static function fromFile(string $filepath, string $namespace): self
	{
		$class_info = new static($namespace);

		$class_info->filepath = $filepath;

		return $class_info;
	}

	public function getNamespace()
	{
		return $this->namespace;
	}

	public function getClass(): string
	{
		if (!$this->class) {
			$this->class = basename($this->filepath, '.php');
		}

		return $this->class;
	}

	public function getFqClass(): string
	{
		return $this->getNamespace() . $this->getClass();
	}

	public function getName(): string
	{
		if (!$this->name) {
			$class = $this->getClass(false);
			$this->name = strtolower($class[0]) . substr($class, 1, -7);
		}

		return $this->name;
	}

	public function load(): void
	{
		if (!class_exists($this->getFqClass())) {
			require_once $this->filepath;
		}
	}

	public function hasMethod(string $method): bool
	{
		$this->load();
		return method_exists($this->getFqClass(), $method);
	}

	/**
	 * @return CommandMethodInfo[]
	 */
	public function getMethods(): array
	{
		if (!$this->methods) {
			$this->load();
			foreach (get_class_methods($this->getFqClass()) as $method) {
				if ($method[0] === '_') {
					continue;
				}

				$this->methods[] = new CommandMethodInfo($this, $method);
			}
		}

		return $this->methods;
	}

	public function getUsage(): string
	{
		$this->load();

		$reflector = new \ReflectionClass($this->getFqClass());
		$docblock = $reflector->getDocComment();

		if ($docblock === false) {
			return '';
		}

		$comment = substr($docblock, 3, (strpos($docblock, '@')) - 3);
		$comment = str_replace(['\r\n', '\n\r', '\r'], '\n', $comment);
		$comment = preg_replace('/(^|\n)\s*\* ?/', '$1', $comment);
		$comment = preg_replace('/\h+($|\n)/', '$1', $comment);

		preg_match('/^(.+)(?:\v\v(.*))?$/suU', $comment, $matches);
		$matches = array_pad($matches, 3, '');
		$usage = str_replace("\n", ' ', $matches[1]);
		$details = str_replace(["\n\n", "\n", "\r"], ["\r", ' ', "\n"], $matches[2]);

		if ($details) {
			$usage .= "\n" . $details;
		}

		return $usage;
	}
}
