<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\DrugBundle\Entity\Article;
use Vidal\DrugBundle\Entity\ArticleRubrique;

class ArticleRemoveCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:article_remove');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em       = $this->getContainer()->get('doctrine')->getManager('drug');
		$articles = $em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			ORDER BY a.priority DESC
		')->getResult();

		$grouped = array();
		$removed = 0;

		foreach ($articles as $a) {
			$key = $a->getOldId();
			if (!isset($grouped[$key])) {
				$grouped[$key] = $a;
			}
			else {
				$em->remove($grouped[$key]);
				$removed++;
				$output->writeln($removed);
			}
		}

		$em->flush();
	}
}