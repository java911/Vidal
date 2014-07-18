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
		$pdo       = $em->getConnection();

		foreach ($companies as $company) {
			$company = trim($company);
			$company = $this->mb_ucfirst($company);

			$stmt = $pdo->prepare("UPDATE tag SET text = '$company' WHERE text LIKE '$company'");
			$stmt->execute();
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