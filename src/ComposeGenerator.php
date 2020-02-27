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

	/**
	 * @var \Laradock\Config
	 */
	protected $config;

	protected $excludeServices = [];

	protected $extraHosts = [];

	/**
	 * @var string An IP of the host
	 */
	protected $dockerHostIp;

	/**
	 * Generate result docker compose file
	 */
	public function generate(): void
	{
		$this->readTemplate();

		$this->config = Config::fromFile($this->configFile);
		$this->excludeServices = [
			'php-fpm-71' => true,
			'php-fpm-72' => true,
			'php-fpm-73' => true,
			'php-fpm-74' => true,
			'elasticsearch6' => true,
		];

		$this->dockerHostIp = trim(file_get_contents(LARADOCK_ROOT . '/var/docker-host-ip'));

		$this->mapAuthorizedSshKey();

		$this->generateVolumes();

		$this->generateSites();

		$this->configureOptionalServices();

		$this->generateWorkspaceArgs();

		$this->generatePortMappings();

		$this->generateExtraHosts();

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
		return $this->config->get('features.ohmyzsh') ? 'zsh' : 'bash';
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
		if (!$this->config['authorize']) {
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
		if (!$this->validateIndexedArray('folders')) {
			return;
		}

		$volumes = [];

		foreach ($this->config->get('folders', []) as $folder) {
			$volumes[] = $folder['map'] . ':' . $folder['to'] . '${APP_CODE_CONTAINER_FLAG}';
		}

		$services = [
			'workspace',
			'nginx',
			'php-fpm-71',
			'php-fpm-72',
			'php-fpm-73',
			'php-fpm-74',
		];
		foreach ($services as $service) {
			$this->configMerge("services.{$service}.volumes", $volumes);
		}
	}

	/**
	 * Generates nginx sites configs
	 */
	protected function generateSites(): void
	{
		if (!$this->validateIndexedArray('sites')) {
			return;
		}

		$sitesDir = __DIR__ . "/../etc/sites";
		if (!is_dir($sitesDir)) {
			mkdir($sitesDir, 0644, true);
		}

		$template = file_get_contents(__DIR__ . '/../resources/vhost.example.conf');
		foreach ($this->config->get('sites', []) as $site) {
			$site += [
				'php' => '7.4',
			];
			$fpmService = 'php-fpm-' . str_replace('.', '', $site['php']);
			file_put_contents(__DIR__ . "/../etc/sites/{$site['map']}.conf", strtr($template, [
				'${domain}' => $site['map'],
				'${root}' => $site['to'],
				'${upstream}' => "{$fpmService}:9000",
			]));

			// remove php-fpm version from excluded services
			unset($this->excludeServices[$fpmService]);

			$this->extraHosts[] = $site['map'] . ':' . $this->dockerHostIp;
		}
	}

	/**
	 * Generates build arguments for workspace
	 */
	protected function generateWorkspaceArgs(): void
	{
		if ($this->config['ubuntu_source']) {
			$this->configMerge('services.workspace.build.args', [
				'CHANGE_SOURCE=true',
				'UBUNTU_SOURCE=' . $this->config['ubuntu_source'],
			]);
		}

		$phpVersions = Arr::wrap($this->config->get( 'php.versions', []));
		$phpVersions = array_intersect($phpVersions, ['5.6', '7.0', '7.1']);
		$this->configMerge('services.workspace.build.args', array_map(function ($version) {
			return 'INSTALL_PHP_' . str_replace('.', '', $version) . '=true';
		}, $phpVersions));

		$phpExtensions = Arr::wrap($this->config->get('php.extensions', []));
		$this->configMerge('services.workspace.build.args', array_map(function ($extension) {
			return 'INSTALL_PHP_' . strtoupper($extension) . '=true';
		}, $phpExtensions));

		$nodejsVersions = Arr::wrap($this->config->get('nodejs.versions', []));
		if (count($nodejsVersions)) {
			$this->configMerge('services.workspace.build.args', [
				'INSTALL_NODEJS=true',
				'INSTALL_NODEJS_VERSIONS=' . join(',', $nodejsVersions),
				'INSTALL_YARN=true',
				'INSTALL_YARN_VERSION=' . Arr::get($this->config, 'nodejs.yarn', 'latest'),
			]);
		}

		if ($this->config->get('features.ohmyzsh') == 'true') {
			$this->configMerge('services.workspace.build.args', ['INSTALL_OH_MY_ZSH=true']);
		}
	}

	protected function generatePortMappings(): void
	{
		if (!$this->config['ports']) {
			echo Color::yellow() . 'WARNING: ';
			echo Color::reset() . 'No port mappings are available. You may not be able to use the services from your host.' . PHP_EOL;
			return;
		}

		$services = array_keys($this->compose['services']);
		foreach ($services as $service) {
			$ports = Arr::wrap($this->config->get('ports.' . $service, []));
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
		if ($this->config->get('features.elasticsearch')) {
			if (in_array(6, Arr::wrap($this->config->get('features.elasticsearch')))) {
				unset($this->excludeServices['elasticsearch6']);
			}
		}
	}

	protected function generateExtraHosts()
	{
		if (empty($this->extraHosts)) {
			return;
		}

		foreach (array_keys($this->compose['services']) as $service) {
			$this->configMerge("services.$service.extra_hosts", $this->extraHosts);
		}
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	protected function validateIndexedArray(string $key): bool
	{
		$valid = is_array($this->config[$key]) && !Arr::isAssoc($this->config[$key]);
		if (!$valid) {
			echo Color::yellow() . 'WARNING: ';
			echo Color::reset() . "Something wrong with your ${key} configuration. Please, refer to docs for proper format." . PHP_EOL;
		}

		return $valid;
	}
}
