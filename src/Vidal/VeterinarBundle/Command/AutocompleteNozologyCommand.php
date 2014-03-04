<?php
namespace Vidal\VeterinarBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации автодополнения показаний Nozology
 *
 * @package Vidal\VeterinarBundle\Command
 */
class AutocompleteNozologyCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('veterinar:autocomplete_nozology')
			->setDescription('Creates autocomplete_nozology type in Elastica');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('--- veterinar:autocomplete_nozology started');

		$em = $this->getContainer()->get('doctrine')->getManager('veterinar');

		$nozologies = $em->getRepository('VidalVeterinarBundle:Nozology')->findAll();

		$elasticaClient = new \Elastica\Client();
		$elasticaIndex  = $elasticaClient->getIndex('website');
		$elasticaType   = $elasticaIndex->getType('autocomplete_nozology');

		# delete if exists
		if ($elasticaType->exists()) {
			$elasticaType->delete();
		}

		# Define mapping
		$mapping = new \Elastica\Type\Mapping();
		$mapping->setType($elasticaType);

		# Set mapping
		$mapping->setProperties(array(
			'code' => array('type' => 'string', 'include_in_all' => TRUE),
			'name' => array('type' => 'string', 'include_in_all' => TRUE),
		));

		# Send mapping to type
		$mapping->send();

		# записываем на сервер документы автодополнения
		$documents = array();

		for ($i = 0; $i < count($nozologies); $i++) {
			$documents[] = new \Elastica\Document($i + 1, array(
				'code' => $nozologies[$i]['NozologyCode'],
				'name' => $nozologies[$i]['Name'],
			));

			if ($i && $i % 500 == 0) {
				$elasticaType->addDocuments($documents);
				$elasticaType->getIndex()->refresh();
				$documents = array();
			}
		}

		$elasticaType->getIndex()->refresh();

		$output->writeln("+++ veterinar:autocomplete_nozology loaded $i documents!");
	}
}