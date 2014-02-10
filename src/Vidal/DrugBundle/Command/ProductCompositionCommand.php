<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда редактирования Product.Composition
 *
 * @package Vidal\DrugBundle\Command
 */
class ProductCompositionCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:product_composition')
			->setDescription('Edits composition of products');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('--- vidal:product_composition started');

		$em = $this->getContainer()->get('doctrine')->getManager();

		$products = $em->createQuery('
			SELECT p.ProductID, p.Composition
			FROM VidalDrugBundle:Product p
			WHERE p.Composition LIKE \'%&loz;%\' OR
				p.Composition LIKE \'%[PRING]%\'
		')->getResult();

		$query = $em->createQuery('
			UPDATE VidalDrugBundle:Product p
			SET p.Composition = :product_composition
			WHERE p = :product_id
		');

		for ($i = 0; $i < count($products); $i++) {
			$patterns     = array('/\[PRING\]/i', '/&loz;/i');
			$replacements = array('<i class"pring">Вспомогательные вещества</i>:', '');
			$composition  = preg_replace($patterns, $replacements, $products[$i]['Composition']);

			$query->setParameters(array(
				'product_composition' => $composition,
				'product_id'          => $products[$i]['ProductID'],
			))->execute();
		}

		$output->writeln('+++ vidal:product_composition completed!');
	}
}