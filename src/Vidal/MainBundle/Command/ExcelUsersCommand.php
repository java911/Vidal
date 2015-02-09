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

		$phpExcelObject->getDefaultStyle()
			->getAlignment()
			->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		$phpExcelObject->setActiveSheetIndex(0);
		$worksheet = $phpExcelObject->getActiveSheet();

		$specialties = array();
		$regions     = array();
		$cities      = array();

		for ($i = 0; $i < count($users); $i++) {
			# заполняем массив по специальности
			$key = $users[$i]['specialty'];
			if (!empty($key)) {
				isset($specialties[$key])
					? $specialties[$key] = $specialties[$key] + 1
					: $specialties[$key] = 1;
			}

			# заполняем массив по региону
			$key = $users[$i]['region'];
			if (!empty($key)) {
				isset($regions[$key])
					? $regions[$key] = $regions[$key] + 1
					: $regions[$key] = 1;
			}

			$key = $users[$i]['city'];
			if (!empty($key)) {
				isset($cities[$key])
					? $cities[$key] = $cities[$key] + 1
					: $cities[$key] = 1;
			}
		}

		# заполняем первую страницу
		$this->firstColumn($worksheet, 'Все пользователи');
		$this->populateWorksheet($worksheet, $users);

		# заполняем вторую страницу со статистикой
		$newsheet = $phpExcelObject->createSheet(NULL, 1);
		$phpExcelObject->setActiveSheetIndex(1);

		arsort($specialties);
		arsort($regions);
		arsort($cities);

		$newsheet
			->setTitle('Сводная статистика')
			->setCellValue('A1', 'Специальность')
			->setCellValue('B1', 'Кол-во')
			->setCellValue('C1', 'Регион')
			->setCellValue('D1', 'Кол-во')
			->setCellValue('E1', 'Город')
			->setCellValue('F1', 'Кол-во');

		$alphabet = explode(' ', 'A B C D E F');

		foreach ($alphabet as $letter) {
			$newsheet->getColumnDimension($letter)->setWidth('30');
			$newsheet->getStyle($letter . '1')->applyFromArray(array(
				'fill' => array(
					'type'  => \PHPExcel_Style_Fill::FILL_SOLID,
					'color' => array('rgb' => 'FF0000')
				),
				'font' => array(
					'bold'  => true,
					'color' => array('rgb' => 'FFFFFF'),
					'size'  => 13,
					'name'  => 'Verdana',
				)
			));
		}

		$i = 2;
		foreach ($specialties as $specialty => $qty) {
			$newsheet->setCellValue("A{$i}", $specialty)->setCellValue("B{$i}", $qty);
			$i++;
		}

		$i = 2;
		foreach ($regions as $region => $qty) {
			$newsheet->setCellValue("C{$i}", $region)->setCellValue("D{$i}", $qty);
			$i++;
		}

		$i = 2;
		foreach ($cities as $city => $qty) {
			$newsheet->setCellValue("E{$i}", $city)->setCellValue("F{$i}", $qty);
			$i++;
		}

		###################################################################################################
		$phpExcelObject->setActiveSheetIndex(0);

		$file = $this->getContainer()->get('kernel')->getRootDir() . DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'download' . DIRECTORY_SEPARATOR
			. ($number ? "users_{$number}.xlsx" : 'users.xlsx');

		$writer = $this->getContainer()->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		$writer->save($file);

		$output->writeln('+++ vidal:excel_users completed!');
	}

	private function firstColumn($worksheet, $title)
	{
		$worksheet
			->setTitle($title)
			->setCellValue('A1', 'Специальность')
			->setCellValue('B1', 'Город')
			->setCellValue('C1', 'Регион')
			->setCellValue('D1', 'Зарегистр.')
			->setCellValue('E1', 'Почтовый адрес')
			->setCellValue('F1', 'ФИО');

		$alphabet = explode(' ', 'A B C D E F');

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
					'size'  => 13,
					'name'  => 'Verdana',
				)
			));
		}
	}

	private function populateWorksheet($worksheet, $users)
	{
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
	}
}