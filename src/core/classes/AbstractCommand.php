<?php
declare(strict_types=1);

abstract class AbstractCommand
{
	/**
	 * @var Cli
	 */
	protected $cli;

	/**
	 * @var League\Container\Container
	 */
	protected $di_container;

	/**
	 * @var League\Tactician\CommandBus
	 */
	protected $command_bus;

	public function __construct()
	{
		$this->cli = new Cli();
		$this->di_container = \DI\Container::instance();
		$this->command_bus = \CommandBus::instance();
	}
}