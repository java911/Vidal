<?php
namespace Vidal\VeterinarBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации названий для автодополнения поисковой строки
 *
 * @package Vidal\VeterinarBundle\Command
 */
class AutocompleteExtCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('veterinar:autocomplete_ext')
			->setDescription('Creates extended autocomplete in Elastica');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('--- veterinar:autocomplete_ext started');

		$em = $this->getContainer()->get('doctrine')->getManager('veterinar');

		$productNames  = $em->getRepository('VidalVeterinarBundle:Product')->findProductNames();
		$moleculeNames = $em->getRepository('VidalVeterinarBundle:Molecule')->findMoleculeNames();
		$nozologyNames = $em->getRepository('VidalVeterinarBundle:Nozology')->findNozologyNames();

		$names = array_unique(array_merge($productNames, $moleculeNames));
		sort($names);
		$names = array_merge($names, $nozologyNames);

		$elasticaClient = new \Elastica\Client();
		$elasticaIndex  = $elasticaClient->getIndex('website');
		$elasticaType   = $elasticaIndex->getType('autocompleteext');

		# delete if exists
		if ($elasticaType->exists()) {
			$elasticaType->delete();
		}

		# Define mapping
		$mapping = new \Elastica\Type\Mapping();
		$mapping->setType($elasticaType);

		# Set mapping
		$mapping->setProperties(array(
			'id'   => array('type' => 'integer', 'include_in_all' => FALSE),
			'name' => array('type' => 'string', 'include_in_all' => TRUE),
		));

		# Send mapping to type
		$mapping->send();

		# записываем на сервер документы автодополнения
		$documents = array();

		for ($i = 0; $i < count($names); $i++) {
			$documents[] = new \Elastica\Document($i + 1, array('name' => $names[$i]));

			if ($i && $i % 500 == 0) {
				$elasticaType->addDocuments($documents);
				$elasticaType->getIndex()->refresh();
				$output->writeln("... + $i");
			}
		}

		$output->writeln("+++ veterinar:autocomplete_ext loaded $i documents!");
	}
}