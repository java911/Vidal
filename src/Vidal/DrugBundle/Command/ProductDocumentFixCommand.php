<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductDocumentFixCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:product_document_fix');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:product_document_fix started');

		$em  = $this->getContainer()->get('doctrine')->getManager('drug');
		$pdo = $em->getConnection();
		$pds = array(
			'39519' => 39625,
			'29867' => 39625,
			'9328'  => 39625,
			'34142' => 39626,
			'38603' => 39627,
			'29875' => 36649,
			'29876' => 36649,
			'18351' => 36620,
			'37309' => 36620,
			'37306' => 36620,
			'6019'  => 36659,
			'6020'  => 36659,
			'37305' => 36659,
			'37308' => 36659,
		);

		$stmt = $pdo->prepare('SET FOREIGN_KEY_CHECKS=0');
		$stmt->execute();
		$stmt = $pdo->prepare('UPDATE product SET document_id = :DocumentID WHERE ProductID = :ProductID');

		foreach ($pds as $ProductID => $DocumentID) {
			$stmt->bindParam(':ProductID', $ProductID);
			$stmt->bindParam(':DocumentID', $DocumentID);
			$stmt->execute();
		}

		$output->writeln('+++ vidal:product_document_fix completed!');
	}
}