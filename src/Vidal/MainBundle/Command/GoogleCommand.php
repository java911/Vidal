<?php
namespace Vidal\MainBundle\Command;

use
	Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
	Symfony\Component\Console\Input\InputArgument,
	Symfony\Component\Console\Input\InputInterface,
	Symfony\Component\Console\Input\InputOption,
	Symfony\Component\Console\Output\OutputInterface,
	Symfony\Component\Process\Process;

class EventCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:event');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		# снимаем ограничение времени выполнения скрипта (в safe-mode не работает)
		set_time_limit(0);

		$container = $this->getContainer();

		$logger    = $container->get('vidal.digest_logger');
		$em        = $container->get('doctrine')->getManager();

		# рассылаем с помощью EventSendCommand
		$command = 'php '
			. $container->get('kernel')->getRootDir()
			. '/console vidal:eventsend ';

        $doctors = $em->createQuery('
                        SELECT e.username
                        FROM VidalMainBundle:User e
                ')->getResult();

        $emails = array();
        foreach ($doctors as $doctor) {
            $emails[] = $doctor['username'];
        }

//        $emails[] = 'tulupov.m@gmail.com';
		$emails = array_diff($emails, $logger->getSentEmails());

		for ($i = 0, $c = count($emails); $i < $c; $i = $i + 100) {
			$emails100 = array_slice($emails, $i, 100);
			$emails100 = implode(' ', $emails100);

			# формируем команду для рассылки соточки
			try {
				$processCmd = $command . $emails100;
				$process    = new Process($processCmd);
				$process->run();
			}
			catch (\Exception $e) {
				continue;
			}

			$process = null;
			sleep(40);
		}
	}
}
