<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда редактирования Document.CompiledComposition
 *
 * @package Vidal\MainBundle\Command
 */
class DocumentCompositionCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:documentcomposition')
			->setDescription('Edit compiled compositoin of document');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = $this->getContainer()->get('doctrine')->getManager();

		$documents = $em->createQuery('
			SELECT d.DocumentID, d.CompiledComposition
			FROM VidalMainBundle:Document d
			WHERE d.CompiledComposition LIKE \'%&loz;%\' OR
				d.CompiledComposition LIKE \'%[PRING]%\'
		')->getResult();

		$query = $em->createQuery('
			UPDATE VidalMainBundle:Document d
			SET d.CompiledComposition = :document_composition
			WHERE d = :document_id
		');

		for ($i = 0; $i < count($documents); $i++) {
			$patterns     = array('/\[PRING\]/', '&loz;');
			$replacements = array('<i class"pring">Вспомогательные вещества</i>:', '');
			$composition  = preg_replace($patterns, $replacements, $documents[$i]['CompiledComposition']);

			$query->setParameters(array(
				'document_composition' => $composition,
				'document_id'          => $documents[$i]['ProductID'],
			))->execute();
		}

		$output->writeln('... vidal:documentcomposition completed!');
	}
}