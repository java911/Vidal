<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации названий для документов (для админки)
 *
 * @package Vidal\DrugBundle\Command
 */
class AutocompleteDocumentCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:autocomplete_document')
			->setDescription('Creates Document name autocomplete in Elastica');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('--- vidal:autocomplete_document started');

		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$names = $em->getRepository('VidalDrugBundle:Document')->findProductNames();

		$names = array_unique($names);

		$elasticaClient = new \Elastica\Client();
		$elasticaIndex  = $elasticaClient->getIndex('website');
		$elasticaType   = $elasticaIndex->getType('autocomplete_document');

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

		for ($i = 0; $i < count($names); $i++) {
			$documents = array();
			$documents[] = new \Elastica\Document($i + 1, array('name' => $names[$i]));

			if ($i && $i % 500 == 0) {
				$elasticaType->addDocuments($documents);
				$elasticaType->getIndex()->refresh();
				$documents = array();
				$output->writeln("... + $i");
			}
		}
		$elasticaType->addDocuments($documents);
		$elasticaType->getIndex()->refresh();

		$output->writeln("+++ vidal:autocomplete_document loaded $i documents!");
	}
}