<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DocumentMoleculeCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:document_molecule');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:document_molecule started');

		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$dis = $em->createQuery('
			SELECT di.DocumentID, di.MoleculeID
			FROM VidalDrugBundle:MoleculeDocument di
		')->getResult();

		$pdo  = $em->getConnection();
		$sql  = 'INSERT INTO document_molecule (DocumentID, MoleculeID) VALUES (?, ?)';
		$stmt = $pdo->prepare($sql);

		foreach ($dis as $di) {
			$stmt->bindParam(1, $di['DocumentID']);
			$stmt->bindParam(2, $di['MoleculeID']);
			$stmt->execute();
		}

		$output->writeln('+++ vidal:document_molecule completed!');
	}
}