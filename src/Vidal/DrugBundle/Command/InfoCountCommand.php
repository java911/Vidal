<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InfoCountCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:info_count')
			->setDescription('InfoPage.countProducts generator');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('+++ vidal:info_command started');

		$em        = $this->getContainer()->get('doctrine')->getManager('drug');
		$repo      = $em->getRepository('VidalDrugBundle:Product');
		$infoPages = $em->getRepository('VidalDrugBundle:InfoPage')->findAll();

		# ставим сколько всего у них препаратов
		foreach ($infoPages as $infoPage) {
			$products = $repo->findByInfoPageID($infoPage->getInfoPageID());
			$count    = count($products);
			$infoPage->setCountProducts($count);
		}

		$em->flush();

		$output->writeln('--- vidal:info_count completed');
	}
}