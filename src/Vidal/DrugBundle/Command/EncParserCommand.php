<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EncParserCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:enc_parser')
			->setDescription('Command to copy');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em       = $this->getContainer()->get('doctrine')->getManager();
		$fileName = __DIR__ . DIRECTORY_SEPARATOR . 'enc2012.xml';
		$fileXml = file_get_contents($fileName);

		$noko = new \nokogiri($fileXml);

		$xml = $noko->get('encyclopedy')->toArray();
		$diseases = $xml[0]['disease'];

		//var_dump($diseases[0]);
	}
}