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
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:autocomplete_document started');

		$em    = $this->getContainer()->get('doctrine')->getManager('drug');
		$names = array();

		$documents = $em->createQuery("
			SELECT d.DocumentID, d.RusName
			FROM VidalDrugBundle:Document d
			WHERE d.CountryEditionCode = 'RUS'
			ORDER BY d.RusName ASC
		")->getResult();

		foreach ($documents as $document) {
			$names[] = $document['DocumentID'] . ' ' . $this->strip($document['RusName']);
		}

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
			$document = new \Elastica\Document($i + 1, array('name' => $names[$i]));
			$elasticaType->addDocument($document);
			$elasticaType->getIndex()->refresh();

			if ($i && $i % 500 == 0) {
				$output->writeln("... + $i");
			}
		}

		$output->writeln("+++ vidal:autocomplete_document loaded $i documents!");
	}

	private function strip($string)
	{
		$pat = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i', '/&amp;/');
		$rep = array('', '', '&');

		return preg_replace($pat, $rep, $string);
	}
}