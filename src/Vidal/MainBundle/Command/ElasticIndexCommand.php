<?php

namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации индекса Эластики
 *
 * @package Vidal\MainBundle\Command
 */
class ElasticIndexCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:elasticindex')
			->setDescription('Creates website index in Elastica');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$elasticaClient = new \Elastica\Client();
		$elasticaIndex  = $elasticaClient->getIndex('website');

		if ($elasticaIndex->exists()) {
			$elasticaIndex->delete();
			$elasticaIndex->create();
			$output->writeln('+++ vidal:elasticindex recreated website index!');
		}
		else {
			$elasticaIndex->create();
			$output->writeln('+++ vidal:elasticindex created website index!');
		}
	}
}