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

		$companies = file(__DIR__ . DIRECTORY_SEPARATOR . 'doc.txt');
		$i         = 0;
		$total     = count($companies);

		foreach ($companies as $company) {
			$company = trim($company);
			$company = $this->mb_ucfirst($company);
			$tag     = $em->getRepository('VidalDrugBundle:Tag')->createOrGet($company);

			# статьи специалистам
			$arts = $em->createQuery('
				SELECT a
				FROM VidalDrugBundle:Art a
				WHERE a.title LIKE :text
					OR a.announce LIKE :text
					OR a.body LIKE :text
			')->setParameter('text', '%' . $company . '%')->getResult();

			if (!empty($arts)) {
				foreach ($arts as $o) {
					$o->addTag($tag);
				}
				$em->flush();
			}

			# статьи энциклопедии
			$articles = $em->createQuery('
				SELECT a
				FROM VidalDrugBundle:Article a
				WHERE a.title LIKE :text
					OR a.announce LIKE :text
					OR a.body LIKE :text
			')->setParameter('text', '%' . $company . '%')->getResult();

			if (!empty($articles)) {
				foreach ($articles as $o) {
					$o->addTag($tag);
				}
				$em->flush();
			}

			# новости
			$publications = $em->createQuery('
				SELECT a
				FROM VidalDrugBundle:Publication a
				WHERE a.title LIKE :text
					OR a.announce LIKE :text
					OR a.body LIKE :text
			')->setParameter('text', '%' . $company . '%')->getResult();

			if (!empty($publications)) {
				foreach ($publications as $o) {
					$o->addTag($tag);
				}
				$em->flush();
			}

			$i++;
			$output->writeln("... $i / $total");
		}

		$output->writeln('+++ vidal:tag completed');
	}

	private function mb_ucfirst($string, $encoding = 'utf-8')
	{
		$strlen    = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then      = mb_substr($string, 1, $strlen - 1, $encoding);

		return mb_strtoupper($firstChar, $encoding) . $then;
	}
}