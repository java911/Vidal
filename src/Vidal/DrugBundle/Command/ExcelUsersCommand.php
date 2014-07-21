<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExcelUsersCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:excel_users');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		$output->writeln('--- vidal:excel_users');

		$em             = $this->getContainer()->get('doctrine')->getManager('drug');
		$users          = $em->getRepository('VidalMainBundle:User')->findUsersExcel();
		$phpExcelObject = $this->getContainer()->get('phpexcel')->createPHPExcelObject();

		$phpExcelObject->getProperties()->setCreator("Vidal.ru")
			->setLastModifiedBy("Vidal.ru")
			->setTitle("Зарегистрированные пользователи Видаля")
			->setSubject("Зарегистрированные пользователи Видаля");

		$phpExcelObject->setActiveSheetIndex(0)
			->setCellValue('A1', 'Почтовый адрес')
			->setCellValue('B1', 'Фамилия')
			->setCellValue('C1', 'Имя')
			->setCellValue('D1', 'Отчество')
			->setCellValue('E1', 'Первичная специальность')
			->setCellValue('F1', 'Вторичная специальность')
			->setCellValue('G1', 'Специализация')
			->setCellValue('H1', 'Университет')
			->setCellValue('I1', 'Другое учебное заведение')
			->setCellValue('J1', 'Год выпуска')
			->setCellValue('K1', 'Форма обучения')
			->setCellValue('L1', 'Ученая степень')
			->setCellValue('M1', 'Дата рождения')
			->setCellValue('N1', 'Номер телефона')
			->setCellValue('O1', 'ICQ')
			->setCellValue('P1', 'Тема диссертации')
			->setCellValue('Q1', 'Профессиональные интересы')
			->setCellValue('R1', 'Место работы')
			->setCellValue('S1', 'Должность')
			->setCellValue('T1', 'Стаж')
			->setCellValue('U1', 'Достижения')
			->setCellValue('V1', 'Публикации')
			->setCellValue('W1', 'О себе');

		$worksheet = $phpExcelObject->getActiveSheet();
		$alphabet  = explode(' ', 'A B C D E F G H I J K L N O P Q R S T U V W');
		foreach ($alphabet as $letter) {
			$worksheet->getColumnDimension($letter)->setAutoSize('true');
		}

		for ($i = 0; $i < count($users); $i++) {
			$index = $i + 2;
			$worksheet
				->setCellValue("A{$index}", $users[$i]['username'])
				->setCellValue("B{$index}", $users[$i]['lastName'])
				->setCellValue("C{$index}", $users[$i]['firstName'])
				->setCellValue("D{$index}", $users[$i]['surName'])
				->setCellValue("E{$index}", $users[$i]['primarySpecialty'])
				->setCellValue("F{$index}", $users[$i]['secondarySpecialty'])
				->setCellValue("G{$index}", $users[$i]['specialization'])
				->setCellValue("H{$index}", $users[$i]['university'])
				->setCellValue("I{$index}", $users[$i]['school'])
				->setCellValue("J{$index}", $users[$i]['graduateYear'] ? $users[$i]['graduateYear']->format('Y') : null)
				->setCellValue("K{$index}", $users[$i]['educationType'])
				->setCellValue("L{$index}", $users[$i]['academicDegree'])
				->setCellValue("M{$index}", $users[$i]['birthdate'] ? $users[$i]['birthdate']->format('d.m.Y') : null)
				->setCellValue("N{$index}", $users[$i]['phone'])
				->setCellValue("N{$index}", $users[$i]['icq'])
				->setCellValue("P{$index}", $users[$i]['dissertation'])
				->setCellValue("Q{$index}", $users[$i]['professionalInterests'])
				->setCellValue("R{$index}", $users[$i]['jobPlace'])
				->setCellValue("S{$index}", $users[$i]['jobPosition'])
				->setCellValue("T{$index}", $users[$i]['jobStage'])
				->setCellValue("U{$index}", $users[$i]['jobAchievements'])
				->setCellValue("V{$index}", $users[$i]['jobPublications'])
				->setCellValue("W{$index}", $users[$i]['about']);
		}

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$phpExcelObject->setActiveSheetIndex(0);

		$file   = '/home/twigavid/vidal/download/users.xls';
		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
		$writer->save($file);

		$output->writeln("+++ vidal:excel_users completed!");
	}
}