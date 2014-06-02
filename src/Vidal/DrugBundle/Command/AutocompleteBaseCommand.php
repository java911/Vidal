<?php

namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации индекса Эластики
 *
 * @package Vidal\DrugBundle\Command
 */
class AutocompleteBaseCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:autocomplete_base')
			->setDescription('Creates website index in Elastica');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:elastic_index started');

		$elasticaClient = new \Elastica\Client();
		$elasticaIndex  = $elasticaClient->getIndex('website');

		$elasticaIndex->create(
			array(
				'number_of_shards'   => 4,
				'number_of_replicas' => 1,
			),
			true
		);

		$output->writeln('+++ vidal:elastic_index created!');
	}
}