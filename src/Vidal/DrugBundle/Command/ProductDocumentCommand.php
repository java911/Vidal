<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductDocumentCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:product_document');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:product_document started');

		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$productDocuments = $em->createQuery('
			SELECT pd.ProductID, pd.DocumentID, d.ArticleID
			FROM VidalDrugBundle:ProductDocument pd
			LEFT JOIN VidalDrugBundle:Document d WITH d.DocumentID = pd.DocumentID
			ORDER BY pd.ProductID ASC
		')->getResult();

		$grouped = array();

		foreach ($productDocuments as $pd) {
			$key             = $pd['ProductID'];
			$grouped[$key][] = $pd;
		}

		$updateQuery = $em->createQuery('
			UPDATE VidalDrugBundle:Product p
			SET p.document = :DocumentID
			WHERE p.ProductID = :ProductID
		');

		$articlePriority = array(2, 5, 4, 3, 1);

		foreach ($grouped as $ProductID => $group) {
			if (count($group) == 1) {
				$DocumentID = $group[0]['DocumentID'];
			}
			else {
				# если документов несколько, то надо взять один по приоритету Document.ArticleID [2,5,4,3,1]
				$curr       = array_search($group[0]['ArticleID'], $articlePriority);
				$DocumentID = $group[0]['DocumentID'];

				foreach ($group as $pd) {
					$next = array_search($pd['ArticleID'], $articlePriority);
					if ($next < $curr) {
						$curr       = $next;
						$DocumentID = $pd['DocumentID'];
					}
				}
			}

			$updateQuery->setParameters(array(
				'DocumentID' => $DocumentID,
				'ProductID'  => $ProductID,
			))->execute();
		}

		$output->writeln('+++ vidal:product_document completed!');
	}
}