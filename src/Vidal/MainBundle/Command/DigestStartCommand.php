<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class DigestStartCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:digest:start')
			->setDescription('Send digest to users');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$container = $this->getContainer();
		$em        = $container->get('doctrine')->getManager();

		$digest = $em->getRepository('VidalMainBundle:Digest')->get();

		if (true == $digest->getProgress()) {
			exec("pgrep digest", $pids);
			if (empty($pids)) {
				$kernel  = $container->get('kernel');
				$cmd     = 'php ' . $kernel->getRootDir() . '/console vidal:digest --all';
				$process = new \Symfony\Component\Process\Process($cmd);

				$process->run();
				$output->writeln('+++ started');
			}
			else {
				$output->writeln('--- digest already running');
			}
		}
		else {
			$output->writeln('--- progress is zero');
		}
	}
}
