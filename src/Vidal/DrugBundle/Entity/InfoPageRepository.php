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
}