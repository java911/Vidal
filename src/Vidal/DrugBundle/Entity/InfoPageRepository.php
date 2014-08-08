<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class InfoPageRepository extends EntityRepository
{
	public function findOneByInfoPageID($InfoPageID)
	{
		return $this->_em->createQuery("
			SELECT i
			FROM VidalDrugBundle:InfoPage i
			LEFT JOIN VidalDrugBundle:Country c WITH i.CountryCode = c
			WHERE i.CountryEditionCode = 'RUS'
				AND i = :InfoPageID
		")->setParameter('InfoPageID', $InfoPageID)
			->getOneOrNullResult();
	}

	public function findByInfoPageID($InfoPageID)
	{
		return $this->_em->createQuery("
			SELECT i.InfoPageID, i.RusName, i.RusAddress, c.RusName Country, i.photo
			FROM VidalDrugBundle:InfoPage i
			LEFT JOIN VidalDrugBundle:Country c WITH i.CountryCode = c
			WHERE i.CountryEditionCode = 'RUS'
				AND i = :InfoPageID
		")->setParameter('InfoPageID', $InfoPageID)
			->getOneOrNullResult();
	}

	public function findByProducts($products)
	{
		$documentIds = array();

		foreach ($products as $product) {
			$key = $product['DocumentID'];
			if (!isset($documentIds[$key])) {
				$documentIds[$key] = '';
			}
		}

		$documentIds = array_keys($documentIds);

		$infoPages = $this->_em->createQuery("
			SELECT DISTINCT i.InfoPageID, i.RusName, c.RusName Country, d.DocumentID
			FROM VidalDrugBundle:InfoPage i
			LEFT JOIN VidalDrugBundle:Country c WITH i.CountryCode = c
			LEFT JOIN i.documents d
			WHERE i.CountryEditionCode = 'RUS'
				AND d.DocumentID IN (:documentIds)
		")->setParameter('documentIds', $documentIds)
			->getResult();

		# надо сгруппировать по ID документа
		$results = array();
		foreach ($infoPages as $infoPage) {
			$key = $infoPage['DocumentID'];
			if (!isset($results[$key])) {
				$results[$key] = $infoPage;
			}
		}

		return $results;
	}

	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery("
			SELECT i.InfoPageID, i.RusName, c.RusName Country
			FROM VidalDrugBundle:InfoPage i
			LEFT JOIN i.documents d
			LEFT JOIN VidalDrugBundle:Country c WITH i.CountryCode = c
			WHERE i.CountryEditionCode = 'RUS'
				AND d.DocumentID = :DocumentID
		")->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function getQuery()
	{
		$qb = $this->_em->createQueryBuilder();

		$qb
			->select('i')
			->from('VidalDrugBundle:InfoPage', 'i')
			->orderBy('i.RusName', 'ASC')
			->where("i.CountryEditionCode = 'RUS'")
			->andWhere('i.countProducts > 0');

		return $qb->getQuery();
	}

	public function getNames()
	{
		$qb = $this->_em->createQueryBuilder();

		$qb
			->select('i.RusName')
			->from('VidalDrugBundle:InfoPage', 'i')
			->orderBy('i.RusName', 'ASC')
			->where("i.CountryEditionCode = 'RUS'");

		$results = $qb->getQuery()->getResult();
		$names   = array();

		foreach ($results as $result) {
			$name = preg_replace('/ &.+; /', ' ', $result['RusName']);
			$name = preg_replace('/&.+;/', ' ', $name);

			$names[] = $name;
		}

		$uniques = array();

		foreach ($names as $name) {
			if (!isset($uniques[$name])) {
				$uniques[$name] = '';
			}
		}

		return array_keys($uniques);
	}

	public function findByLetter($l)
	{
		return $this->_em->createQuery('
			SELECT i
			FROM VidalDrugBundle:InfoPage i
			JOIN i.documents d
			WHERE i.RusName LIKE :letter
				AND i.countProducts > 0
			ORDER BY i.RusName ASC
		')->setParameter('letter', $l . '%')
			->getResult();
	}

	public function findByQuery($q)
	{
		$words = explode(' ', $q);

		$qb = $this->_em->createQueryBuilder();
		$qb->select('i')->from('VidalDrugBundle:InfoPage', 'i')->orderBy('i.RusName', 'ASC');

		# поиск по всем словам вместе
		$qb->where('i.countProducts > 0')->andWhere($this->where($words, 'AND'));
		$results = $qb->getQuery()->getResult();

		if (!empty($results)) {
			return $results;
		}

		foreach ($words as $word) {
			if (mb_strlen($word, 'utf-8') < 3) {
				return array();
			}
		}

		# поиск по одному из слов
		$words = $this->getWords($q);
		$qb->where('i.countProducts > 0')->andWhere($this->where($words, 'OR'));
		$results = $qb->getQuery()->getResult();

		return $results;
	}

	public function findPortfolios($InfoPageID)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT p
			FROM VidalDrugBundle:PharmPortfolio p
			JOIN p.DocumentID d
			JOIN d.infoPages i
			WHERE i.InfoPageID = :InfoPageID
		')->setParameter('InfoPageID', $InfoPageID)
			->getResult();
	}

	private function where($words, $s)
	{
		$s = ($s == 'OR') ? ' OR ' : ' AND ';

		$i     = 0;
		$where = '';

		foreach ($words as $word) {
			if ($i > 0) {
				$where .= $s;
			}
			$i++;
			$where .= "(i.RusName LIKE '$word%' OR i.RusName LIKE '% $word%' OR i.EngName LIKE '$word%' OR i.EngName LIKE '% $word%')";
		}

		return $where;
	}

	private function getWords($q)
	{
		$words     = explode(' ', $q);
		$isRussian = preg_match('/^[а-яё\s]+$/iu', $q);

		$rus = array(
			'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'АЙ', 'Й',
			'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
			'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
			'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'ай', 'й',
			'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
			'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
		);

		$rus2 = array(
			'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
			'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
			'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я',
			'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
			'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
			'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я',
		);

		$eng = array(
			'A', 'B', 'V', 'G', 'D', 'E', 'IO', 'ZH', 'Z', 'I', 'Y',
			'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F',
			'H', 'TS', 'CH', 'SH', 'SCH', '', 'Y', '', 'E', 'YU', 'IA',
			'a', 'b', 'v', 'g', 'd', 'e', 'io', 'zh', 'z', 'i', 'y',
			'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f',
			'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ia',
		);

		if ($isRussian) {
			$words = array_merge($words, explode(' ', str_replace($rus, $eng, $q)));
			$words = array_merge($words, explode(' ', str_replace($rus2, $eng, $q)));
		}
		else {
			$words = array_merge($words, explode(' ', str_replace($eng, $rus, $q)));
			$words = array_merge($words, explode(' ', str_replace($eng, $rus2, $q)));
		}

		return array_unique($words);
	}
}