<?php

namespace Docker;

use Symfony\Component\Yaml\Yaml;

class ComposeGenerator
{
	protected $templateFile = null;

	protected $configFile = null;

	protected $outputFile = null;

	protected $compose = [];

	/**
	 * Generate result docker compose file
	 */
	public function generate()
	{
		$this->readTemplate();
		$config = Yaml::parse(file_get_contents($this->configFile));

		if (isset($config['folders'])) {
			$volumes = [];

			foreach ($config['folders'] as $folder) {
				$volumes[] = $folder['map'] . ':' . $folder['to'] . ':cached';
			}

			$services = [
				'workspace',
				'nginx',
				'php-fpm-71',
				'php-fpm-72',
				'php-fpm-73',
			];
			foreach ($services as $service) {
				$this->configMerge("services.{$service}.volumes", $volumes);
			}
		}

		$excludeServices = [
			'php-fpm-71' => true,
			'php-fpm-72' => true,
			'php-fpm-73' => true,
			'elasticsearch6' => true,
		];

		if (isset($config['sites'])) {
			$sitesDir = __DIR__ . "/../etc/sites";
			if (!is_dir($sitesDir)) {
				mkdir($sitesDir, 0644, true);
			}

			$template = file_get_contents(__DIR__  . '/../resources/vhost.example.conf');
			foreach ($config['sites'] as $site) {
				$site += [
					'php' => '7.2',
				];
				file_put_contents(__DIR__ . "/../etc/sites/{$site['map']}.conf", strtr($template, [
					'${domain}' => $site['map'],
					'${root}' => $site['root'],
					'${upstream}' => "php{$site['php']}-upstream",
				]));

				// remove php-fpm version from excluded services
				unset($excludeServices['php-fpm-' . str_replace('.', '', $site['php'])]);
			}
		}

		if (isset($config['features']['elasticsearch'])) {
			if (in_array(6, Arr::wrap($config['features']['elasticsearch']))) {
				unset($excludeServices['elasticsearch6']);
			}
		}

		$this->compose['services'] = Arr::except($this->compose['services'], array_keys($excludeServices));
	}

	/**
	 * Write result config into output file
	 *
	 * @return bool|int
	 */
	public function write()
	{
		return file_put_contents($this->outputFile, Yaml::dump($this->compose, 8, 2));
	}

	/**
	 * Read config from template
	 */
	protected function readTemplate()
	{
		$this->compose = Yaml::parse(file_get_contents($this->templateFile));
	}

	/**
	 * Merge values into existing array
	 *
	 * @param string $key
	 * @param array $values
	 */
	protected function configMerge(string $key, array $values)
	{
		$current = Arr::get($this->compose, $key);
		if (!is_array($current)) {
			$current = [];
		}

		Arr::set($this->compose, $key ,array_merge($current, $values));
	}

	/**
	 * @param string $templateFile
	 */
	public function setTemplateFile($templateFile)
	{
		$this->templateFile = $templateFile;
	}

	/**
	 * @param string $configFile
	 */
	public function setConfigFile($configFile)
	{
		$this->configFile = $configFile;
	}

	/**
	 * @param string $outputFile
	 */
	public function setOutputFile($outputFile)
	{
		$this->outputFile = $outputFile;
	}
}
