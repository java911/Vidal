<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:p');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:p started');

		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$pdo = $em->getConnection();

		# publication
		$publications = $em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.atcCodes IS NOT EMPTY
		')->getResult();

		$stmt = $pdo->prepare('INSERT IGNORE INTO publication_product (ProductID, publication_id) VALUES (?, ?)');

		foreach ($publications as $publication) {
			foreach ($publication->getAtcCodes() as $atc) {
				foreach ($atc->getProducts() as $product) {
					$ProductID     = $product->getProductID();
					$publicationId = $publication->getId();
					$stmt->bindParam(1, $ProductID);
					$stmt->bindParam(2, $publicationId);
					$stmt->execute();
				}
			}
		}

		# art
		$arts = $em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.atcCodes IS NOT EMPTY
		')->getResult();

		$stmt = $pdo->prepare('INSERT IGNORE INTO art_product (ProductID, art_id) VALUES (?, ?)');

		foreach ($arts as $art) {
			foreach ($art->getAtcCodes() as $atc) {
				foreach ($atc->getProducts() as $product) {
					$ProductID = $product->getProductID();
					$id        = $art->getId();
					$stmt->bindParam(1, $ProductID);
					$stmt->bindParam(2, $id);
					$stmt->execute();
				}
			}
		}

		# article
		$arts = $em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE a.atcCodes IS NOT EMPTY
		')->getResult();

		$stmt = $pdo->prepare('INSERT IGNORE INTO article_product (ProductID, article_id) VALUES (?, ?)');

		foreach ($arts as $art) {
			foreach ($art->getAtcCodes() as $atc) {
				foreach ($atc->getProducts() as $product) {
					$ProductID = $product->getProductID();
					$id        = $art->getId();
					$stmt->bindParam(1, $ProductID);
					$stmt->bindParam(2, $id);
					$stmt->execute();
				}
			}
		}

		$output->writeln("+++ vidal:p completed!");
	}
}