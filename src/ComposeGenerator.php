<?php

namespace Laradock;

use Codedungeon\PHPCliColors\Color;
use Symfony\Component\Yaml\Yaml;

class ComposeGenerator
{
	protected $templateFile = null;

	protected $configFile = null;

	protected $outputFile = null;

	protected $compose = [];

	protected $config = [];

	protected $excludeServices = [];

	/**
	 * Generate result docker compose file
	 */
	public function generate(): void
	{
		$this->readTemplate();

		$this->config = Yaml::parse(file_get_contents($this->configFile));
		$this->excludeServices = [
			'php-fpm-71' => true,
			'php-fpm-72' => true,
			'php-fpm-73' => true,
			'elasticsearch6' => true,
		];

		$this->mapAuthorizedSshKey();

		$this->generateVolumes();

		$this->generateSites();

		$this->configureOptionalServices();

		$this->generateWorkspaceArgs();

		$this->generatePortMappings();

		$this->compose['services'] = Arr::except($this->compose['services'], array_keys($this->excludeServices));
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
	 * Resolves shell executable to login into workspace
	 *
	 * @return string
	 */
	public function resolveShell(): string
	{
		return isset($this->config['features']['ohmyzsh']) ? 'zsh' : 'bash';
	}

	/**
	 * Read config from template
	 */
	protected function readTemplate(): void
	{
		$this->compose = Yaml::parse(file_get_contents($this->templateFile));
	}

	/**
	 * Merge values into existing array
	 *
	 * @param string $key
	 * @param array $values
	 */
	protected function configMerge(string $key, array $values): void
	{
		$current = Arr::get($this->compose, $key);
		if (!is_array($current)) {
			$current = [];
		}

		Arr::set($this->compose, $key, array_merge($current, $values));
	}

	/**
	 * @param string $templateFile
	 */
	public function setTemplateFile($templateFile): void
	{
		$this->templateFile = $templateFile;
	}

	/**
	 * @param string $configFile
	 */
	public function setConfigFile($configFile): void
	{
		$this->configFile = $configFile;
	}

	/**
	 * @param string $outputFile
	 */
	public function setOutputFile($outputFile): void
	{
		$this->outputFile = $outputFile;
	}

	/**
	 * Generate volume mapping from config
	 */
	protected function mapAuthorizedSshKey(): void
	{
		if (!isset($this->config['authorize'])) {
			return;
		}

		$this->configMerge('services.workspace.volumes', [
			"{$this->config['authorize']}:/etc/laradock/ssh_authorize"
		]);
	}

	/**
	 * Generate volume mapping from config
	 */
	protected function generateVolumes(): void
	{
		if (isset($this->config['folders'])) {
			$volumes = [];

			foreach ($this->config['folders'] as $folder) {
				$volumes[] = $folder['map'] . ':' . $folder['to'] . '${APP_CODE_CONTAINER_FLAG}';
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
	}

	/**
	 * Generates nginx sites configs
	 */
	protected function generateSites(): void
	{
		if (isset($this->config['sites'])) {
			$sitesDir = __DIR__ . "/../etc/sites";
			if (!is_dir($sitesDir)) {
				mkdir($sitesDir, 0644, true);
			}

			$template = file_get_contents(__DIR__ . '/../resources/vhost.example.conf');
			foreach ($this->config['sites'] as $site) {
				$site += [
					'php' => '7.2',
				];
				$fpmService = 'php-fpm-' . str_replace('.', '', $site['php']);
				file_put_contents(__DIR__ . "/../etc/sites/{$site['map']}.conf", strtr($template, [
					'${domain}' => $site['map'],
					'${root}' => $site['to'],
					'${upstream}' => "{$fpmService}:9000",
				]));

				// remove php-fpm version from excluded services
				unset($this->excludeServices[$fpmService]);
			}
		}
	}

	/**
	 * Generates build arguments for workspace
	 */
	protected function generateWorkspaceArgs(): void
	{
		if (isset($this->config['ubuntu_source'])) {
			$this->configMerge('services.workspace.build.args', [
				'CHANGE_SOURCE=true',
				'UBUNTU_SOURCE=' . $this->config['ubuntu_source'],
			]);
		}

		$phpVersions = Arr::wrap(Arr::get($this->config, 'php.versions', []));
		$phpVersions = array_intersect($phpVersions, ['5.6', '7.0', '7.1', '7.2', '7.3']);
		$this->configMerge('services.workspace.build.args', array_map(function ($version) {
			return 'INSTALL_PHP_' . str_replace('.', '', $version) . '=true';
		}, $phpVersions));

		$phpExtensions = Arr::wrap(Arr::get($this->config, 'php.extensions', []));
		$this->configMerge('services.workspace.build.args', array_map(function ($extension) {
			return 'INSTALL_PHP_' . strtoupper($extension) . '=true';
		}, $phpExtensions));

		$nodejsVersions = Arr::wrap(Arr::get($this->config, 'nodejs.versions', []));
		if (count($nodejsVersions)) {
			$this->configMerge('services.workspace.build.args', [
				'INSTALL_NODEJS=true',
				'INSTALL_NODEJS_VERSIONS=' . join(',', $nodejsVersions),
				'INSTALL_YARN=true',
				'INSTALL_YARN_VERSION=' . Arr::get($this->config, 'nodejs.yarn', 'latest'),
			]);
		}

		if (isset($this->config['features']['ohmyzsh'])) {
			$this->configMerge('services.workspace.build.args', ['INSTALL_OH_MY_ZSH=true']);
		}
	}

	protected function generatePortMappings(): void
	{
		if (!isset($this->config['ports'])) {
			echo Color::yellow() . 'WARNING: ';
			echo Color::reset() . 'No port mappings are available. You may not be able to use the services from your host.' . PHP_EOL;
			return;
		}

		$services = array_keys($this->compose['services']);
		foreach ($services as $service) {
			$ports = Arr::wrap(Arr::get($this->config['ports'], $service, []));
			if (empty($ports)) {
				continue;
			}

			$this->configMerge('services.' . $service . '.ports', $ports);
		}
	}

	/**
	 * Optional services enabling/disabling/configuration
	 */
	protected function configureOptionalServices(): void
	{
		if (isset($this->config['features']['elasticsearch'])) {
			if (in_array(6, Arr::wrap($this->config['features']['elasticsearch']))) {
				unset($this->excludeServices['elasticsearch6']);
			}
		}
	}
}
