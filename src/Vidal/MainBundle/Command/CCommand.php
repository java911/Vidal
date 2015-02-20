<?php
namespace Vidal\MainBundle\Command;

use
	Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Output\OutputInterface,
	Symfony\Component\Process\Process;

class CCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:c');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		# снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
		set_time_limit(0);

		$container = $this->getContainer();
		$em        = $container->get('doctrine')->getManager();
		$pdo       = $em->getConnection();

		$pdo->prepare('SET foregin_key_checks = 0')->execute();
		$pdo2 = new \PDO('mysql:host=localhost;dbname=c', 'root', null);

		$citiesStmt = $pdo2->prepare('SELECT DISTINCT name FROM cities WHERE country_id = ?');


	}
}
