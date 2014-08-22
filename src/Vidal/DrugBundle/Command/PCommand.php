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

		######################################
		$arts = $em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE SIZE(a.documents) > 0
		')->getResult();

		foreach ($arts as $art) {
			foreach ($art->getDocuments() as $document) {
				foreach ($document->getProducts() as $product) {
					$art->addProduct($product);
				}
			}
		}

		######################################
		$arts = $em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE SIZE(a.documents) > 0
		')->getResult();

		foreach ($arts as $art) {
			foreach ($art->getDocuments() as $document) {
				foreach ($document->getProducts() as $product) {
					$art->addProduct($product);
				}
			}
		}

		######################################
		$arts = $em->createQuery('
			SELECT a
			FROM VidalDrugBundle:PharmArticle a
			WHERE SIZE(a.documents) > 0
		')->getResult();

		foreach ($arts as $art) {
			foreach ($art->getDocuments() as $document) {
				foreach ($document->getProducts() as $product) {
					$art->addProduct($product);
				}
			}
		}

		######################################
		$arts = $em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Publication a
			WHERE SIZE(a.documents) > 0
		')->getResult();

		foreach ($arts as $art) {
			foreach ($art->getDocuments() as $document) {
				foreach ($document->getProducts() as $product) {
					$art->addProduct($product);
				}
			}
		}

		$em->flush();

		$output->writeln("+++ vidal:p completed!");
	}
}