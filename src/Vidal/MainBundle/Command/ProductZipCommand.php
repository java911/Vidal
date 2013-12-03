<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда удаления из ZipInfo препарата символов ромбика &loz;
 *
 * @package Vidal\MainBundle\Command
 */
class ProductZipCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:productzip')
			->setDescription('Removes &loz; from Product.ZipInfo');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = $this->getContainer()->get('doctrine')->getManager();

		$products = $em->createQuery('
			SELECT p.ProductID, p.ZipInfo
			FROM VidalMainBundle:Product p
			WHERE p.ZipInfo LIKE \'%&loz;%\'
		')->getResult();

		$query = $em->createQuery('
			UPDATE VidalMainBundle:Product p
			SET p.ZipInfo = :product_zip
			WHERE p = :product_id
		');

		for ($i = 0; $i < count($products); $i++) {
			$zip = preg_replace('/&loz;/i', '', $products[$i]['ZipInfo']);

			$query->setParameters(array(
				'product_zip' => $zip,
				'product_id'  => $products[$i]['ProductID'],
			))->execute();
		}

		$output->writeln('... vidal:productzip completed!');
	}
}