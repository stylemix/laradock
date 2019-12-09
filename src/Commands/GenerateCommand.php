<?php

namespace Docker\Commands;

use Docker\ComposeGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{

	protected static $defaultName = 'generate';

	protected function configure()
	{
		$this
			->setDescription('Generate')
			->setHelp('Generated final docker-compose from user configuration')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$templateFile = realpath(__DIR__ . '/../../resources/docker-compose.example.yml');
		$configFile = realpath(__DIR__ . '/../../config.yml');
		$outputFile = realpath(__DIR__ . '/../..') . '/docker-compose.yml';

		$generator = new ComposeGenerator();
		$generator->setTemplateFile($templateFile);
		$generator->setConfigFile($configFile);
		$generator->setOutputFile($outputFile);
		$generator->generate();
		if (!$generator->write()) {
			$output->writeln('Error writing to: ' . $outputFile);
			return 1;
		}

		$output->writeln('Configuration generated.');

		return 0;
	}
}
