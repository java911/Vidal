<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\EntityRepository;

class InfoPageRepository extends EntityRepository
{
	public function findByLetter($l)
	{
		return $this->_em->createQuery('
			SELECT i.InfoPageID, i.RusName, c.RusName Country, i.Name
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			WHERE i.RusName LIKE :letter
			ORDER BY i.RusName ASC
		')->setParameter('letter', $l . '%')
			->getResult();
	}

	public function findByQuery($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb
			->select('i.InfoPageID, i.RusName, country.RusName Country, i.Name')
			->from('VidalVeterinarBundle:InfoPage', 'i')
			->leftJoin('VidalVeterinarBundle:Country', 'country', 'WITH', 'country.CountryCode = i.CountryCode')
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

	public function findByInfoPageID($InfoPageID)
	{
		return $this->_em->createQuery('
			SELECT i.InfoPageID, i.RusName, i.RusAddress, c.RusName Country, i.Name
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			WHERE i = :InfoPageID
		')->setParameter('InfoPageID', $InfoPageID)
			->getOneOrNullResult();
	}

	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT i.InfoPageID, i.RusName, c.RusName Country, i.Name
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN VidalVeterinarBundle:DocumentInfoPage di WITH di.InfoPageID = i
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			WHERE di.DocumentID = :DocumentID
			ORDER BY di.Ranking DESC
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function findOneByName($name)
	{
		return $this->_em->createQuery('
			SELECT i.InfoPageID, i.RusName, i.RusAddress, c.RusName Country, i.Name
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			WHERE i.Name = :name
		')->setParameter('name', $name)
			->getOneOrNullResult();
	}

	public function findAllOrdered()
	{
		return $this->_em->createQuery('
			SELECT i.Name, i.RusName, c.RusName Country
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			ORDER BY i.RusName
		')->getResult();
	}
}