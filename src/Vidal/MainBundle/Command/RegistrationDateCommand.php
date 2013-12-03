<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Команда генерации нормальных дат препаратов
 *
 * @package Vidal\MainBundle\Command
 */
class RegistrationDateCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:registrationdate')
			->setDescription('Transforms dates of product registration to NULL if its like 0000-00-00 00:00:00');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em = $this->getContainer()->get('doctrine')->getManager();

		$em->createQuery('
			UPDATE VidalMainBundle:Product p
			SET p.RegistrationDate = NULL
			WHERE p.RegistrationDate = \'0000-00-00 00:00:00\'
		')->execute();

		$em->createQuery('
			UPDATE VidalMainBundle:Product p
			SET p.DateOfCloseRegistration = NULL
			WHERE p.DateOfCloseRegistration = \'0000-00-00 00:00:00\'
		')->execute();

		$output->writeln('... vidal:registrationdate completed!');
	}
}