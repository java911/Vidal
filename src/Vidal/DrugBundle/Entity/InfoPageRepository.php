<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class InfoPageRepository extends EntityRepository
{
	public function findByInfoPageID($InfoPageID)
	{
		return $this->_em->createQuery("
			SELECT i.InfoPageID, i.RusName, i.RusAddress, c.RusName Country
			FROM VidalDrugBundle:InfoPage i
			LEFT JOIN VidalDrugBundle:Country c WITH i.CountryCode = c
			WHERE i.CountryEditionCode = 'RUS'
				AND i = :InfoPageID
		")->setParameter('InfoPageID', $InfoPageID)
			->getOneOrNullResult();
	}

	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery("
			SELECT i.InfoPageID, i.RusName, c.RusName Country
			FROM VidalDrugBundle:InfoPage i
			LEFT JOIN VidalDrugBundle:DocumentInfoPage di WITH di.InfoPageID = i
			LEFT JOIN VidalDrugBundle:Country c WITH i.CountryCode = c
			WHERE i.CountryEditionCode = 'RUS'
				AND di.DocumentID = :DocumentID
			ORDER BY di.Ranking DESC
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
			->where("i.CountryEditionCode = 'RUS'");

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
			SELECT i.InfoPageID, i.RusName, c.RusName Country
			FROM VidalDrugBundle:InfoPage i
			LEFT JOIN VidalDrugBundle:Country c WITH i.CountryCode = c
			WHERE i.RusName LIKE :letter
			ORDER BY i.RusName ASC
		')->setParameter('letter', $l . '%')
			->getResult();
	}

	public function findByQuery($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb
			->select('i.InfoPageID, i.RusName, country.RusName Country')
			->from('VidalDrugBundle:InfoPage', 'i')
			->leftJoin('VidalDrugBundle:Country', 'country', 'WITH', 'country.CountryCode = i.CountryCode')
			->orderBy('i.RusName', 'ASC');

		$where = '';
		$words = explode(' ', $q);

		# поиск по всем словам вместе
		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= ' AND ';
			}
			$where .= "(i.RusName LIKE '$word%' OR i.RusName LIKE '% $word%')";
		}

		$qb->where($where);
		$results = $qb->getQuery()->getResult();

		# поиск по одному слову
		if (empty($results)) {
			$where = '';
			for ($i = 0; $i < count($words); $i++) {
				$word = $words[$i];
				if ($i > 0) {
					$where .= ' OR ';
				}
				$where .= "(i.RusName LIKE '$word%' OR i.RusName LIKE '% $word%')";
			}
			$qb->where($where);

			return $qb->getQuery()->getResult();
		}

		return $results;
	}
}