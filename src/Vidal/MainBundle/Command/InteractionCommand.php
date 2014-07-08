<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InteractionCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:interaction');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:interaction started');

		$em = $this->getContainer()->get('doctrine')->getManager();

		$excel = \PHPExcel_IOFactory::load(__DIR__ . DIRECTORY_SEPARATOR . '1.xls');
		$excel->setActiveSheetIndex(0);

		$sheet         = $excel->getActiveSheet();
		$highestRow    = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();

		//  Loop through each row of the worksheet in turn
		for ($row = 1; $row <= $highestRow; $row++){
			$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
			$rowData = $rowData[0];

			var_dump($rowData);exit;
		}

		$output->writeln('+++ vidal:interaction completed');
	}
}