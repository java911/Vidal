<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации нормальных имен для препаратов
 *
 * @package Vidal\MainBundle\Command
 */
class DocumentNameCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:documentname')
			->setDescription('Adds names to document without fucking tags');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('--- vidal:documentname started');

		$em = $this->getContainer()->get('doctrine')->getManager();

		# надо установить имена для препаратов без тегов в нижний регистр
		$em->createQuery('
			UPDATE VidalMainBundle:Document d
			SET d.Name = LOWER(d.EngName)
			WHERE d.EngName NOT LIKE \'%<%\'
		')->execute();

		# теперь надо удалить из имен теги и записать в БД
		$documents = $em->createQuery('
			SELECT d.DocumentID, d.EngName
			FROM VidalMainBundle:Document d
			WHERE d.EngName LIKE \'%<%\'
		')->getResult();

		$query = $em->createQuery('
			UPDATE VidalMainBundle:Document d
			SET d.Name = :document_name
			WHERE d = :document_id
		');

		for ($i = 0; $i < count($documents); $i++) {
			$p    = array('/ /', '/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i');
			$r    = array('-', '', '');
			$name = preg_replace($p, $r, $documents[$i]['EngName']);
			$name = mb_strtolower($name, 'UTF-8');

			$query->setParameters(array(
				'document_name' => $name,
				'document_id'   => $documents[$i]['DocumentID'],
			))->execute();
		}

		$output->writeln('+++ vidal:documentname completed!');
	}
}