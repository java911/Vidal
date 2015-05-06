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
			exec("/bin/ps -axw", $out);

			if (!preg_match('/vidal:digest --all/', implode(' ', $out))) {
				$cmd = 'nohup php ' . $container->get('kernel')->getRootDir() . '/console vidal:digest --all > /home/twigavid/c.log 2>&1 &';
				system($cmd);

				$output->writeln('+++ started: ' . $cmd);
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
