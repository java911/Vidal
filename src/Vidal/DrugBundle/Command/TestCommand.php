<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this
			->setName('vidal:_')
			->setDescription('Command to copy')
			//->addArgument('user', InputArgument::OPTIONAL, '')
			//->addOption('email', null, InputOption::VALUE_NONE, '')
		;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$output->writeln('--- vidal: started');

		$output->writeln('+++ vidal: completed');
	}
}