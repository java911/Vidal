<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации названий для автодополнения поисковой строки
 *
 * @package Vidal\DrugBundle\Command
 */
class NozologyTotalCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:nozology_total')
			->setDescription('Fills Nozology.total with count of products');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:nozology_total started');

		$em         = $this->getContainer()->get('doctrine')->getManager('drug');
		$nozologies = $em->getRepository('VidalDrugBundle:Nozology')->findAll();
		$i          = 1;

		foreach ($nozologies as $nozology) {
			$Code = $nozology->getCode();

			$documents = $em->getRepository('VidalDrugBundle:Document')->findByNozologyCode($Code);

			if (!empty($documents)) {
				$molecules = $em->getRepository('VidalDrugBundle:Molecule')->findByDocuments1($documents);
				$products1 = $em->getRepository('VidalDrugBundle:Product')->findByDocuments25($documents);
				$products2 = $em->getRepository('VidalDrugBundle:Product')->findByDocuments4($documents);
				$total     = count($molecules) + count($products1) + count($products2);
				$nozology->setTotal($total);
				$output->writeln('... ' . $i);
			}
			$i++;
		}

		$em->flush();

		$output->writeln("+++ vidal:nozology_total completed!");
	}
}