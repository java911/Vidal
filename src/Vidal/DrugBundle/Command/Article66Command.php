<?php
namespace Vidal\DrugBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Vidal\DrugBundle\Entity\Article;
use Vidal\DrugBundle\Entity\ArticleRubrique;

class Article66Command extends ContainerAwareCommand
{
	protected function configure()
	{
		$this->setName('vidal:article66');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$em   = $this->getContainer()->get('doctrine')->getManager('drug');
		$pdo  = $em->getConnection();
		$stmt = $pdo->prepare('SELECT * FROM message66');
		$stmt->execute();
		$results = $stmt->fetchAll();
		$i       = 0;

		foreach ($results as $r) {
			$a = new Article();
			$a->setTitle($r['Title']);
			$a->setAnnounce($r['Announce']);
			$a->setBody($r['Text']);
			$a->setPublic(false);
			$link = $r['Keyword'] == '' ? $this->translit($r['Title']) : $r['Keyword'];
			$a->setLink($link);
			$a->setSynonym($r['Synonym']);
			$a->setMetaTitle($r['MetaTitle']);
			$a->setMetaDescription($r['MetaDesc']);
			$a->setMetaKeywords($r['MetaKeys']);
			$subId = (int) $r['Subdivision_ID'];
			$a->setSubdivisionId($subId);
			$a->setSubclassId($r['Sub_Class_ID']);
			$a->setOldId($r['Message_ID']);

			$date    = new \DateTime($r['Date']);
			$created = new \DateTime($r['Created']);
			$updated = new \DateTime($r['LastUpdated']);
			$a->setDate($date);
			$a->setCreated($created);
			$a->setUpdated($updated);

			if ($subdivision = $em->getRepository('VidalDrugBundle:Subdivision')->findOneById($subId)) {
				$a->setSubdivision($subdivision);
			}

			$i++;
			$em->persist($a);
			$em->flush($a);
			$output->writeln($i);
		}
	}

	private function translit($text)
	{
		$text = preg_replace('/&[a-z]+;/', '', $text);
		$text = mb_strtolower($text, 'utf-8');

		// Русский алфавит
		$rus_alphabet = array(
			'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
			'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
			'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
			'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
			'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
			'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
			' ', '.', '(', ')', ',', '/', ':', ';', '«', '»', '?', '®', '+'
		);

		// Английская транслитерация
		$rus_alphabet_translit = array(
			'A', 'B', 'V', 'G', 'D', 'E', 'IO', 'ZH', 'Z', 'I', 'Y',
			'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F',
			'H', 'TS', 'CH', 'SH', 'SCH', '', 'Y', '', 'E', 'YU', 'IA',
			'a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'y',
			'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f',
			'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ia',
			'_', '_', '_', '_', '_', '_', '', '', '', '', '_', '', ''
		);

		return str_replace($rus_alphabet, $rus_alphabet_translit, $text);
	}
}