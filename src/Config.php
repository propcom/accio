<?php
declare(strict_types=1);

namespace Propcom\Accio;

class Config
{

	/**
	 * @var array
	 */
	private $data;

	public function __construct(string $config_filepath)
	{
		try {
			$this->data = $this->load($config_filepath);
		} catch (\Exception $e) {
			echo $e->getMessage();
			die(1);
		}
	}

	public function load(string $filepath): array
	{
		if (!is_file($filepath)) {
			throw new \RuntimeException('Can\'t find config file');
		}

		$data = file_get_contents($filepath);
		if ($data === false) {
			throw new \RuntimeException('Can\'t load config file');
		}

		$data = json_decode($data, true);
		if ($data === null) {
			throw new \RuntimeException('Can\'t parse config file');
		}

		return $this->validate($data);
	}

	public function validate(array $data): array
	{
		if (!array_key_exists('directories', $data)) {
			throw new \RuntimeException('Accio config must include the "directories" key');
		}

		if (!is_array($data['directories'])) {
			throw new \RuntimeException('Accio config key "directories" must be an array');
		}

		if (array_key_exists('app_bootstrap', $data) and !is_file($data['app_bootstrap'])) {
			throw new \RuntimeException('Accio config key "app_bootstrap" must be a path to a file');
		}

		return array_intersect_key($data, ['directories' => null, 'app_bootstrap' => null]);
	}

	public function bootstrap(): void
	{
		if ($this->data['app_bootstrap']) {
			require_once $this->data['app_bootstrap'];
		}
	}

	public function getDirectories(): array
	{
		return $this->data['directories'];
	}
}
