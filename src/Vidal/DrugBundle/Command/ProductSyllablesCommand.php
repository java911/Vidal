<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductSyllablesCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:product_syllables');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:product_syllables started');

		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$types = array('p', 'b', 'o');

		foreach ($types as $t) {
			list($syllables, $table) = $em->getRepository('VidalDrugBundle:Product')->findByProductType($t);
			$path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Generated' . DIRECTORY_SEPARATOR;

			$file = "{$path}syllables_{$t}.json";
			file_put_contents($file, json_encode($syllables));

			$file = "{$path}table_{$t}.json";
			file_put_contents($file, json_encode($table));
		}

		$output->writeln('+++ vidal:product_syllables completed!');
	}
}