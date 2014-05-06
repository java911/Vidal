<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductCompanyCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:product_company');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:product_company started');

		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$pcs = $em->createQuery('
			SELECT pc.ItsMainCompany, pc.ProductID, pc.CompanyID,
			FROM VidalDrugBundle:ProductCompany pc
		')->getResult();

		$pdo  = $em->getConnection();
		$sql  = 'INSERT INTO document_molecule (DocumentID, MoleculeID) VALUES (?, ?)';
		$stmt = $pdo->prepare($sql);

		foreach ($dis as $di) {
			$stmt->bindParam(1, $di['DocumentID']);
			$stmt->bindParam(2, $di['MoleculeID']);
			$stmt->execute();
		}

		$output->writeln('+++ vidal:product_company completed!');
	}
}