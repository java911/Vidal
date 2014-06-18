<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\DrugBundle\Entity\Tag;

class TagCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:tag');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$output->writeln('--- vidal:tag started');

		$articles = $em->createQuery('
			SELECT a
			FROM VidalDrugBundle:PharmArticle a
		')->getResult();

		foreach ($articles as $article) {
			if ($company = $article->getCompany()) {
				$text = $company->getTitle();
				$tag = $em->createQuery('
					SELECT t
					FROM VidalDrugBundle:Tag t
					WHERE t.text = :text
				')->setParameter('text', $text)
					->getOneOrNullResult();

				if (!$tag) {
					$tag = new Tag();
					$tag->setText($text);
					$em->persist($tag);
				}

				$article->addTag($tag);
				$em->flush();
			}
		}

		$output->writeln('+++ vidal:tag completed');
	}
}