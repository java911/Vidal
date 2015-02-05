<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExcelUsersCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:excel_users')
			->addArgument('numbers', InputArgument::IS_ARRAY, 'Number of year or month');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		ini_set('max_execution_time', 0);

		$em             = $this->getContainer()->get('doctrine')->getManager();
		$phpExcelObject = $this->getContainer()->get('phpexcel')->createPHPExcelObject();
		$numbers        = $input->getArgument('numbers');
		$number         = empty($numbers) ? null : intval($numbers[0]);
		$users          = $em->getRepository('VidalMainBundle:User')->forExcel($number);

		$phpExcelObject->getProperties()->setCreator('Vidal.ru')
			->setLastModifiedBy('Vidal.ru')
			->setTitle('Зарегистрированные пользователи Видаля')
			->setSubject('Зарегистрированные пользователи Видаля');

		$phpExcelObject->setActiveSheetIndex(0)
			->setCellValue('A1', 'Специальность')
			->setCellValue('B1', 'Город')
			->setCellValue('C1', 'Регион')
			->setCellValue('D1', 'Зарегистр.')
			->setCellValue('E1', 'Почтовый адрес')
			->setCellValue('F1', 'ФИО');

		$worksheet = $phpExcelObject->getActiveSheet();
		$alphabet  = explode(' ', 'A B C D E F');
		foreach ($alphabet as $letter) {
			$worksheet->getColumnDimension($letter)->setWidth('30');
			$worksheet->getStyle($letter . '1')->applyFromArray(array(
				'fill' => array(
					'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'FF0000')
				),
				'font' => array(
					'bold'  => true,
					'color' => array('rgb' => 'FFFFFF'),
					'size'  => 14,
					'name'  => 'Verdana',
				)
			));
		}

		for ($i = 0; $i < count($users); $i++) {
			$index = $i + 2;
			$name  = $users[$i]['lastName'] . ' ' . $users[$i]['firstName'];
			if (!empty($users[$i]['surName'])) {
				$name .= ' ' . $users[$i]['surName'];
			}

			$worksheet
				->setCellValue("A{$index}", $users[$i]['specialty'])
				->setCellValue("B{$index}", $users[$i]['city'])
				->setCellValue("C{$index}", $users[$i]['region'])
				->setCellValue("D{$index}", $users[$i]['registered'])
				->setCellValue("E{$index}", $users[$i]['username'])
				->setCellValue("F{$index}", $name);
		}

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$phpExcelObject->setActiveSheetIndex(0);

		$file = $this->getContainer()->get('kernel')->getRootDir() . DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'download' . DIRECTORY_SEPARATOR
			. ($number ? "users_{$number}.xlsx" : 'users.xlsx');

		$writer = $this->getContainer()->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		$writer->save($file);

		$output->writeln('+++ vidal:excel_users completed!');
	}
}