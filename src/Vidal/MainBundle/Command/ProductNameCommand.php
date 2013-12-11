<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации нормальных имен для препаратов
 *
 * @package Vidal\MainBundle\Command
 */
class ProductNameCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:productname')
			->setDescription('Adds names to product without fucking tags');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('--- vidal:productname started');

		$em = $this->getContainer()->get('doctrine')->getManager();

		$count = $em->createQuery('
			SELECT COUNT(p.ProductID)
			FROM VidalMainBundle:Product p
			WHERE p.CountryEditionCode = \'RUS\' AND
				p.MarketStatusID IN (1,2)  AND
				p.ProductTypeCode IN (\'DRUG\', \'GOME\')
		')->getSingleScalarResult();

		$query = $em->createQuery('
			SELECT p.ProductID, p.EngName
			FROM VidalMainBundle:Product p
			WHERE p.CountryEditionCode = \'RUS\' AND
				p.MarketStatusID IN (1,2)  AND
				p.ProductTypeCode IN (\'DRUG\', \'GOME\')
		');

		$updateQuery = $em->createQuery('
			UPDATE VidalMainBundle:Product p
			SET p.Name = :product_name
			WHERE p = :product_id
		');

		$step = 100;

		for ($i = 0, $c = $count; $i < $c; $i = $i+$step) {
			$products = $query
				->setFirstResult($i)
				->setMaxResults($i+$step)
				->getResult();

			foreach ($products as $product) {
				$p    = array('/ /', '/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i');
				$r    = array('-', '', '');
				$name = preg_replace($p, $r, $product['EngName']);
				$name = mb_strtolower($name, 'UTF-8');

				$updateQuery->setParameters(array(
					'product_name' => $name,
					'product_id'   => $product['ProductID'],
				))->execute();
			}

			$output->writeln("... " . ($i+$step) . " / $count done");
		}

		$output->writeln("+++ vidal:productname updated $i products!");
	}
}