<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Vidal\MainBundle\Entity\AstrazenecaRegion;
use Vidal\MainBundle\Entity\AstrazenecaMap;

class EmailCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:email');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:email started');



		$output->writeln('+++ vidal:email created!');
	}
}