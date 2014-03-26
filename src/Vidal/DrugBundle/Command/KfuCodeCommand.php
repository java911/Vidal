<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class KfuCodeCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:kfu_code')
			->setDescription('Команда помещает разделы в один раздел');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$output->writeln('+++ vidal:kfu_code started');

		$em = $this->getContainer()->get('doctrine')->getManager('drug');

		$changes = array(
			'56' => 23,
			'52' => 11,
			'50' => 1,
			'51' => 2,
			'55' => 22,
			'57' => 26,
			'12' => 53,
			'59' => 29,
			'58' => 28,
			'54' => 15,
		);

		foreach ($changes as $from => $to) {
			$output->writeln("... $from => $to");

			$pointers = $em->createQuery('
				SELECT c
				FROM VidalDrugBundle:ClinicoPhPointers c
				WHERE c.Code LIKE :from
			')->setParameter('from', $from . '.%')
				->getResult();

			foreach ($pointers as $p) {
				$code    = $p->getCode();
				$numbers = explode('.', $code);

				$numbers[0] = $to;
				$numbers[1] = intval($numbers[1]) + 50;

				$numbersStr = implode('.', $numbers);
				if ($to < 10) {
					$numbersStr = '0' . $numbersStr;
				}
				$p->setCode($numbersStr);
			}

			$fromItem = $em->createQuery('
				SELECT c
				FROM VidalDrugBundle:ClinicoPhPointers c
				WHERE c.Code = :from
			')->setParameter('from', $from)
				->getSingleResult();

			if ($fromItem) {
				$em->remove($fromItem);
			}
		}

		$em->flush();

		$output->writeln('--- vidal:kfu_code completed');
	}
}