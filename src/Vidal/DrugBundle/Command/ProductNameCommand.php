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
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:product_name started');

		$em = $this->getContainer()->get('doctrine')->getManager('drug');
		$pdo = $em->getConnection();

		$pdo->prepare("UPDATE product SET Name = REPLACE(EngName,'<SUP>','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'</SUP>','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'<SUB>','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'</SUB>','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'<BR/>','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'<BR />','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'&reg;','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'&amp;','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'&trade;','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'&alpha;','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'&beta;','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'&plusmn;','')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,' - ','_')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,' ','_')")->execute();
		$pdo->prepare("UPDATE product SET Name = REPLACE(Name,'__','_')")->execute();
		$pdo->prepare("UPDATE product SET Name = LOWER(Name)")->execute();

		$output->writeln("+++ vidal:product_name completed!");
	}
}