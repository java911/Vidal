<?php
namespace Vidal\VeterinarBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MarketStatusCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:v_market_status');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:v_market_status');

		$em  = $this->getContainer()->get('doctrine')->getManager('veterinar');
		$pdo = $em->getConnection();

		$updateStmt = $pdo->prepare('UPDATE product SET MarketStatusID = 1 WHERE MarketStatusID = 0');
		$updateStmt->execute();

		$output->writeln('+++ vidal:v_market_status completed!');
	}
}