<?php
namespace Vidal\MainBundle\Command;

use
	Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Output\OutputInterface,
	Symfony\Component\Process\Process;

class GoogleCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:google');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		# снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
		set_time_limit(0);


		$container = $this->getContainer();
		$em        = $container->get('doctrine')->getManager();

		$rootDir = $container->get('kernel')->getRootDir();

		require_once $rootDir . '/Google/Client.php';
		require_once $rootDir . '/Google/Service.php';


	}
}
