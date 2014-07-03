<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SitemapGeneratorCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:sitemap:generator');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:sitemap:generator started');

		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$products = $em->createQuery('

		')->getResult();

		var_dump($products);
		exit;

		$users = $em->createQuery('
			SELECT u
			FROM VidalMainBundle:User u
			WHERE u.city IS NOT NULL
		')->getResult();

		foreach ($users as $user) {
			$city = $user->getCity();
			if ($region = $city->getRegion()) {
				$user->setRegion($region);
			}
			if ($country = $city->getCountry()) {
				$user->setCountry($country);
			}
		}

		$em->flush();

		$output->writeln('+++ vidal:sitemap:generator completed');
	}
}