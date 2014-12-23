<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ATCRepository extends EntityRepository
{
	public function findByLetter($l)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:ATC a
			WHERE a.ATCCode LIKE :l
			ORDER BY a.ATCCode ASC
		')->setParameter('l', $l . '%')
			->getResult();
	}

	public function findOneByATCCode($ATCCode)
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalDrugBundle:ATC a
		 	WHERE a = :ATCCode
		')->setParameter('ATCCode', $ATCCode)
			->getOneOrNullResult();
	}

	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:ATC a
			JOIN a.documents d WITH d = :DocumentID
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function findByProducts($productIds)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT a
			FROM VidalDrugBundle:ATC a
			JOIN a.products p
			WHERE p IN (:productIds)
		')->setParameter('productIds', $productIds)
			->getResult();
	}

	public function findByQuery($q)
	{
		if ($code = strstr($q, ' > ', true)) {
			$atcCodesRaw = $this->_em->createQuery('
				SELECT DISTINCT a.ATCCode, a.RusName, a.EngName, a.ParentATCCode
				FROM VidalDrugBundle:ATC a
				WHERE a.ATCCode = :code
				ORDER BY a.ATCCode ASC
			')->setParameter('code', $code)
				->getResult();
		}
		else {
			$qb = $this->_em->createQueryBuilder();
			$qb->select('DISTINCT a.ATCCode, a.RusName, a.EngName, a.ParentATCCode')
				->from('VidalDrugBundle:ATC', 'a')
				->where('a.ATCCode LIKE :q')
				->orderBy('a.ATCCode', 'ASC')
				->setParameter('q', $q . '%');

			# поиск по всем словам вместе
			$words = explode(' ', $q);
			$where = '';

			for ($i = 0; $i < count($words); $i++) {
				$word = $words[$i];
				if ($i > 0) {
					$where .= ' AND ';
				}
				$where .= "(a.RusName LIKE '$word%' OR a.EngName LIKE '$word%' OR a.RusName LIKE '% $word%' OR a.EngName LIKE '% $word%')";
			}

			$qb->orWhere($where);
			$atcCodesRaw = $qb->getQuery()->getResult();

			# поиск по одному из слов
			if (empty($atcCodesRaw)) {
				foreach ($words as $word) {
					if (mb_strlen($word, 'utf-8') < 3) {
						return array();
					}
				}

				$where = '';

				for ($i = 0; $i < count($words); $i++) {
					$word = $words[$i];
					if ($i > 0) {
						$where .= ' OR ';
					}
					$where .= "(a.RusName LIKE '$word%' OR a.EngName LIKE '$word%' OR a.RusName LIKE '% $word%' OR a.EngName LIKE '% $word%')";
				}

				$qb->where('a.ATCCode LIKE :q');
				$qb->orWhere($where);

				$atcCodesRaw = $qb->getQuery()->getResult();

			}
		}

		$atcCodes = array();

		for ($i = 0, $c = count($atcCodesRaw); $i < $c; $i++) {
			$key            = $atcCodesRaw[$i]['ATCCode'];
			$atcCodes[$key] = $atcCodesRaw[$i];
		}

		return $atcCodes;
	}

	public function countProducts()
	{
		return $this->_em->createQuery('
		 	SELECT a.ATCCode, COUNT(p.ProductID) as countProducts
			FROM VidalDrugBundle:Product p
			JOIN p.atcCodes a
			WHERE p.MarketStatusID IN (1,2)
				AND p.ProductTypeCode IN (\'DRUG\',\'GOME\')
				AND p.inactive = FALSE
			GROUP BY a
		')->getResult();
	}

	public function findForTree()
	{
		return $this->_em->createQuery("
			SELECT a.ATCCode id, a.RusName text
			FROM VidalDrugBundle:ATC a
			WHERE a.ParentATCCode = ''
			ORDER BY a.ATCCode ASC
		")->getResult();
	}

	public function jsonForTree()
	{
		$atcRaw = $this->_em->createQuery('
			SELECT a.ATCCode id, a.RusName text, a.ParentATCCode, a.countProducts
			FROM VidalDrugBundle:ATC a
			ORDER BY a.ATCCode ASC
		')->getResult();

		$atc = array();

		for ($i = 0; $i < count($atcRaw); $i++) {
			$key       = $atcRaw[$i]['id'];
			$atc[$key] = $atcRaw[$i];
		}

		return $atc;
	}

	public function findAutocomplete()
	{
		$atcCodes = $this->_em->createQuery('
			SELECT a.ATCCode, a.RusName, a.EngName
			FROM VidalDrugBundle:ATC a
		')->getResult();

		$atcNames = array();

		for ($i = 0; $i < count($atcCodes); $i++) {
			$patterns     = array('/<SUP>.*<\/SUP>/', '/<SUB>.*<\/SUB>/', '/&alpha;/', '/&amp;/');
			$replacements = array('', '', ' ', ' ');
			$RusName      = preg_replace($patterns, $replacements, $atcCodes[$i]['RusName']);
			$RusName      = mb_strtolower(str_replace('  ', ' ', $RusName), 'UTF-8');
			$EngName      = preg_replace($patterns, $replacements, $atcCodes[$i]['EngName']);
			$EngName      = mb_strtolower(str_replace('  ', ' ', $EngName), 'UTF-8');

			if (!empty($RusName)) {
				$atcNames[] = mb_strtolower($atcCodes[$i]['ATCCode'], 'UTF-8') . ' > ' . $RusName;
			}

			if (!empty($EngName)) {
				$atcNames[] = mb_strtolower($atcCodes[$i]['ATCCode'], 'UTF-8') . ' > ' . $EngName;
			}
		}

		$atcNames = array_unique($atcNames);
		usort($atcNames, 'strcasecmp');

		return $atcNames;
	}

	public function getOptions()
	{
		$raw = $this->_em->createQuery('
			SELECT a.ATCCode, a.RusName, a.EngName
			FROM VidalDrugBundle:ATC a
		 	ORDER BY a.ATCCode ASC
		 ')->getResult();

		$items = array();

		foreach ($raw as $r) {
			$title = $r['ATCCode'] . ' - ' . $r['RusName'];
			if (!empty($r['EngName'])) {
				$title .= ' (' . $r['EngName'] . ')';
			}
			$items[] = array(
				'id'    => $r['ATCCode'],
				'title' => $title
			);
		}

		return $items;
	}

	public function getChoices()
	{
		$raw = $this->_em->createQuery('
			SELECT a.ATCCode, a.RusName, a.EngName
			FROM VidalDrugBundle:ATC a
		 	ORDER BY a.ATCCode ASC
		 ')->getResult();

		$items = array();

		foreach ($raw as $r) {
			$key   = $r['ATCCode'];
			$title = $r['ATCCode'] . ' - ' . $r['RusName'];
			if (!empty($r['EngName']) && $r['EngName'] != $r['RusName']) {
				$title .= ' (' . $r['EngName'] . ')';
			}
			$items[$key] = $title;
		}

		return $items;
	}

	public function getParent($products)
	{
		$atcCodes = $products->getAtcCodes();

		if (empty($atcCodes)) {
			return null;
		}

		foreach ($atcCodes as $atc) {
			$parentCode = $atc->getParentATCCode();

			if (!empty($parentCode)) {
				$parentAtc = $this->_em->createQuery('
					SELECT a
					FROM VidalDrugBundle:ATC a
					WHERE a.ATCCode = :atc
				')->setParameter('atc', $parentCode)
					->getOneOrNullResult();

				if (!empty($parentAtc)) {
					return $parentAtc;
				}
			}
		}

		return null;
	}
}