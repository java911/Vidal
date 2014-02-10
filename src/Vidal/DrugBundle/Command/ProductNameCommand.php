<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации нормальных имен для препаратов
 *
 * @package Vidal\DrugBundle\Command
 */
class ProductNameCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:product_name')
			->setDescription('Adds Product.Name');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('--- vidal:product_name started');

		$em = $this->getContainer()->get('doctrine')->getManager();

		# надо установить имена для препаратов без тегов/пробелов в нижний регистр
		$em->createQuery('
			UPDATE VidalDrugBundle:Product p
			SET p.Name = LOWER(p.EngName)
			WHERE p.EngName NOT LIKE \'%<%\' AND
				p.EngName NOT LIKE \'% %\' AND
				p.EngName NOT LIKE \'%/%\'
		')->execute();

		# далее надо преобразовать остальные по регуляркам
		$count = $em->createQuery('
			SELECT COUNT(p.ProductID)
			FROM VidalDrugBundle:Product p
			WHERE p.CountryEditionCode = \'RUS\' AND
				p.MarketStatusID IN (1,2)  AND
				p.ProductTypeCode IN (\'DRUG\', \'GOME\') AND
				(p.EngName LIKE \'%<%\' OR p.EngName LIKE \'% %\' OR p.EngName LIKE \'%/%\')
		')->getSingleScalarResult();

		$query = $em->createQuery('
			SELECT p.ProductID, p.EngName
			FROM VidalDrugBundle:Product p
			WHERE p.CountryEditionCode = \'RUS\' AND
				p.MarketStatusID IN (1,2)  AND
				p.ProductTypeCode IN (\'DRUG\', \'GOME\') AND
				(p.EngName LIKE \'%<%\' OR p.EngName LIKE \'% %\' OR p.EngName LIKE \'%/%\')
		');

		$updateQuery = $em->createQuery('
			UPDATE VidalDrugBundle:Product p
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
				$name = preg_replace('/\\//', '-', $name);
				$name = mb_strtolower($name, 'UTF-8');

				$updateQuery->setParameters(array(
					'product_name' => $name,
					'product_id'   => $product['ProductID'],
				))->execute();
			}

			$output->writeln("... " . ($i+$step) . " / $count done");
		}

		$output->writeln("+++ vidal:product_name updated $count products!");
	}
}