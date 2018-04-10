<?php

if (PHP_SAPI !== 'cli') {
	throw new \Exception("Only invoke this from the CLI");
}

require __DIR__.'/../../../autoload.php';

$command_router = new \Propcom\Accio\CommandRouter([__DIR__.'/../' => 'Testing']);

$input = new \Propcom\Accio\CliInput($argv);
$output = new \Propcom\Accio\CliOutput();

$cli = new Propcom\Accio\Accio($command_router);
$cli->run($input, $output);
