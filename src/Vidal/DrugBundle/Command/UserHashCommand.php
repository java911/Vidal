<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UserHashCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:user_hash');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:user_hash started');

		$em = $this->getContainer()->get('doctrine')->getManager();

		$users = $em->createQuery("
			SELECT u
			FROM VidalMainBundle:User u
			WHERE u.hash IS NULL
				OR u.hash = ''
		")->getResult();

		foreach ($users as $user)
		{
			$user->refreshHash();
		}

		$em->flush();

		$output->writeln("+++ vidal:user_hash completed!");
	}
}