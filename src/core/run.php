<?php

$cli = new Cli();
$command_info = new CommandInfo();

if (PHP_SAPI !== 'cli') {
	throw new \Exception("Only invoke this from the CLI");
}

if ($argc < 2) {
	$cli->write("Usage:");
	foreach ($command_info->getDefinedCommands() as $command) {
		$cli->write("\t".$command);
	}
	$cli->write("");

	exit(1);
}

array_shift($argv);

$route = new CommandRouter($argv[0]);

if (!$route->isValidCommand()) {
	$cli->error("The command '{$route->getCommand()}' does not exist");
	exit(1);
}

if (!$route->isValidFunction()) {
	$cli->error("The function '{$route->getFunction()}' does not exist");
	exit(1);
}

try {
	$route->run(
		InputFactory::instance()->createInput($argv)
	);
} catch(\Exception $e) {
	$cli->error($e->getMessage());
	$cli->write("");
	exit(1);
}

$cli->write("");