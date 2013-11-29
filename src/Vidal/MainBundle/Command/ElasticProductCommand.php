<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации названий и идентификаторов продуктов
 *
 * @package Vidal\MainBundle\Command
 */
class ElasticProductCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:elastic:product')
			->setDescription('Creates autocomplete in Elastica');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = $this->getContainer()->get('doctrine')->getManager();
	}
}