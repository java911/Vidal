<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации дерева АТС в файл JSON
 *
 * @package Vidal\MainBundle\Command
 */
class TreeAtcCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:tree_atc')
			->setDescription('Generates json-tree for ATC codes');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('--- vidal:tree_atc started');

		$em = $this->getContainer()->get('doctrine')->getManager();
		$atcCodes = $em->getRepository('VidalMainBundle:ATC')->findAll();
		$atcGrouped = array();

		# надо сгруппировать по родителю
		for ($i=7; $i>1; $i--) {
			foreach ($atcCodes as $code => $atc) {
				if (strlen($code) == $i && isset($atc['ParentATCCode'])) {
					$key = $atc['ParentATCCode'];
					$code = $atc['ATCCode'];
					$atcCodes[$key]['list'][$code] = $atc;
				}
			}
		}

		# взять только первый уровень [A, B, C]
		foreach ($atcCodes as $code => $atc) {
			if (strlen($code) == 1) {
				$atcGrouped[$code] = $atc;
			}
		}

		$output->writeln('+++ vidal:tree_atc completed!');
	}
}