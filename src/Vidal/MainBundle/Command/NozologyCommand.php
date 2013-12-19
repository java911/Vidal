<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации показаний Nozology
 *
 * @package Vidal\MainBundle\Command
 */
class NozologyCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:nozology')
			->setDescription('Creates nozology type in Elastica');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('--- vidal:nozology started');

		$em = $this->getContainer()->get('doctrine')->getManager();

		$nozologies = $em->getRepository('VidalMainBundle:Nozology')->findAll();

		$elasticaClient = new \Elastica\Client();
		$elasticaIndex  = $elasticaClient->getIndex('website');
		$elasticaType   = $elasticaIndex->getType('nozology');

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

		for ($i = 0; $i < count($nozologies); $i++) {
			$documents[] = new \Elastica\Document($i + 1, array(
				'code' => $nozologies[$i]['NozologyCode'],
				'name' => $nozologies[$i]['Name'],
			));

			if ($i && $i % 500 == 0) {
				$elasticaType->addDocuments($documents);
				$elasticaType->getIndex()->refresh();
			}
		}

		$output->writeln("+++ vidal:nozology loaded $i documents!");
	}
}