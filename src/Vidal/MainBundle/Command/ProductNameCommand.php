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

		# надо установить имена для препаратов без тегов
		$em->createQuery('
			UPDATE VidalMainBundle:Product p
			SET p.Name = LOWER(p.EngName)
			WHERE p.EngName NOT LIKE \'%<%\'
		')->execute();

		# теперь надо удалить из имен теги и записать в БД
		$products = $em->createQuery('
			SELECT p.ProductID, p.EngName
			FROM VidalMainBundle:Product p
			WHERE p.EngName LIKE \'%<%\'
		')->getResult();

		$query = $em->createQuery('
			UPDATE VidalMainBundle:Product p
			SET p.Name = :product_name
			WHERE p = :product_id
		');

		for ($i = 0; $i < count($products); $i++) {
			$p    = array('/ /', '/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i');
			$r    = array('-', '', '');
			$name = preg_replace($p, $r, $products[$i]['EngName']);
			$name = mb_strtolower($name, 'UTF-8');

			$query->setParameters(array(
				'product_name' => $name,
				'product_id'   => $products[$i]['ProductID'],
			))->execute();
		}

		$output->writeln('+++ vidal:productname completed!');
	}
}