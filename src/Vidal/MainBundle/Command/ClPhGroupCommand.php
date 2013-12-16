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
class ClPhGroupCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:clphgroup')
			->setDescription('Creates clphgroup type in Elastica');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('--- vidal:clphgroup started');

		$em = $this->getContainer()->get('doctrine')->getManager();

		$clphgroup = $em->getRepository('VidalMainBundle:Product')->findClPhGroup();

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
			'code' => array('type' => 'string', 'include_in_all' => TRUE),
			'name' => array('type' => 'string', 'include_in_all' => TRUE, 'analyzer' => 'ru'),
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

		$output->writeln("+++ vidal:clphgroup loaded $i documents!");
	}
}