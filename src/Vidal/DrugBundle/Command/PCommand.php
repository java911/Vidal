<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:p');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:p started');

		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$tags = $em->createQuery('
			SELECT t
			FROM VidalDrugBundle:Tag t
			JOIN t.infoPage
		')->getResult();

		foreach ($tags as $tag) {
			$infoPage = $tag->getInfoPage();

			foreach ($tag->getArts() as $art) {
				$art->addInfoPage($infoPage);
			}

			foreach ($tag->getArticles() as $article) {
				$article->addInfoPage($infoPage);
			}

			foreach ($tag->getPublications() as $publication) {
				$publication->addInfoPage($infoPage);
			}
		}

		$em->flush();

		$output->writeln("+++ vidal:p completed!");
	}
}