<?php
namespace Vidal\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExcelWerCommand extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:excel_wer')
			->addArgument('numbers', InputArgument::IS_ARRAY, 'Number of year or month');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		ini_set('memory_limit', -1);
		ini_set('max_execution_time', 0);

		$container      = $this->getContainer();
		$emDrug         = $container->get('doctrine')->getManager('drug');
		$em             = $container->get('doctrine')->getManager();
		$phpExcelObject = $container->get('phpexcel')->createPHPExcelObject();

		$phpExcelObject->getProperties()->setCreator('Vidal.ru')
			->setLastModifiedBy('Vidal.ru')
			->setTitle('Зарегистрированные пользователи Видаля')
			->setSubject('Зарегистрированные пользователи Видаля');

		$phpExcelObject
			->getDefaultStyle()
			->getAlignment()
			->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
			->setWrapText(true)
			->setVertical(\PHPExcel_Style_Alignment::VERTICAL_TOP);

		$phpExcelObject->setActiveSheetIndex(0);
		$worksheet = $phpExcelObject->getActiveSheet();

		# заголовки
		$worksheet
			->setTitle('Препараты Vidal - Wer.ru')
			->setCellValue('A1', 'Название')
			->setCellValue('B1', 'Vidal.ru')
			->setCellValue('C1', 'Wer.ru');

		$alphabet = explode(' ', 'A B C');

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

			$letter == 'A'
				? $worksheet->getColumnDimension($letter)->setWidth(35)
				: $worksheet->getColumnDimension($letter)->setWidth(70);
		}

		$drugs = $emDrug->createQuery('
			SELECT p.RusName, p.Name, p.ProductID
			FROM VidalDrugBundle:Product p
			WHERE p.MarketStatusID IN (1,2,7)
				AND p.ProductTypeCode IN (\'DRUG\',\'GOME\')
				AND p.inactive = FALSE
		')
			//->setMaxResults(100)
			->getResult();

		$groups = array();
		$router = $container->get('router');

		foreach ($drugs as $drug) {
			$p     = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i');
			$r     = array('', '');
			$title = preg_replace($p, $r, $drug['RusName']);
			$first = mb_substr($title, 0, 2);//первая буква
			$last  = mb_substr($title, 2);//все кроме первой буквы
			$last  = mb_strtolower($last, 'UTF-8');
			$title = $first . $last;

			if (isset($groups[$title])) {
				$url                          = 'http://www.vidal.ru' . $router->generate('product', array(
						'EngName'   => $drug['Name'],
						'ProductID' => $drug['ProductID']
					));
				$groups[$title]['products'][] = $url;
			}
			else {
				$groups[$title]             = array();
				$url                        = 'http://www.vidal.ru' . $router->generate('product', array(
						'EngName'   => $drug['Name'],
						'ProductID' => $drug['ProductID']
					));
				$groups[$title]['products'] = array($url);
			}
		}

		$query = $em->createQuery("
			SELECT md.code, md.title
			FROM VidalMainBundle:MarketDrug md
			WHERE md.title LIKE :title
				AND md.groupApt = 'wer'
		");

		foreach ($groups as $title => &$data) {
			$wers = $query->setParameter('title', $title . '%')->getResult();

			if (!empty($wers)) {
				$strings = array();
				foreach ($wers as $wer) {
					$strings[] = $wer['code'] . ' -- ' . $wer['title'];
				}

				$groups[$title]['wers'] = $strings;
			}
		}

		# данные
		$index = 2;
		foreach ($groups as $title => $data) {
			if (isset($data['wers'])) {
				$worksheet
					->setCellValue("A{$index}", $title)
					->setCellValue("B{$index}", implode("\n", $data['products']))
					->setCellValue("C{$index}", implode("\n", $data['wers']));
				$index++;
			}
		}

		###################################################################################################
		$phpExcelObject->setActiveSheetIndex(0);

		$file = $this->getContainer()->get('kernel')->getRootDir() . DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'download' . DIRECTORY_SEPARATOR
			. 'wer.xlsx';

		$writer = $this->getContainer()->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		$writer->save($file);

		$output->writeln('+++ vidal:excel_wer completed!');
	}
}