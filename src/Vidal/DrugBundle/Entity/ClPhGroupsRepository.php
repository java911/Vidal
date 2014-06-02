<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ClPhGroupsRepository extends EntityRepository
{
	public function findOneById($id)
	{
		return $this->_em->createQuery('
			SELECT g
			FROM VidalDrugBundle:ClPhGroups g
			WHERE g = :id
		')->setParameter('id', $id)
			->getOneOrNullResult();
	}

	public function findWithProducts()
	{
		return $this->_em->createQuery('
			SELECT DISTINCT g, COUNT(p) AS HIDDEN total
			FROM VidalDrugBundle:ClPhGroups g
			JOIN g.products p WITH p.ProductTypeCode IN (\'DRUG\', \'GOME\')
			GROUP BY g
			HAVING total > 0
		')->getResult();
	}

	public function getQuery()
	{
		return $this->_em->createQuery('
			SELECT DISTINCT g, COUNT(p) AS HIDDEN total
			FROM VidalDrugBundle:ClPhGroups g
			JOIN g.products p WITH p.ProductTypeCode IN (\'DRUG\', \'GOME\')
			GROUP BY g
			HAVING total > 0
			ORDER BY g.Name ASC
		')->getResult();
	}

	public function findByLetter($l)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT g, COUNT(p) AS HIDDEN total
			FROM VidalDrugBundle:ClPhGroups g
			JOIN g.products p WITH p.ProductTypeCode IN (\'DRUG\', \'GOME\')
			WHERE g.Name LIKE :letter
			GROUP BY g
			HAVING total > 0
			ORDER BY g.Name ASC
		')->setParameter('letter', $l . '%')
			->getResult();
	}

	public function findByQuery($q)
	{
		$words = explode(' ', $q);

		$qb = $this->_em->createQueryBuilder();
		$qb->select('DISTINCT g, COUNT(p) AS HIDDEN total')
			->from('VidalDrugBundle:ClPhGroups', 'g')
			->join('g.products', 'p', 'WITH', 'p.ProductTypeCode IN (\'DRUG\', \'GOME\')')
			->groupBy('g')
			->having('total > 0')
			->orderBy('g.Name', 'ASC');

		# поиск по всем словам вместе
		$qb->where($this->where($words, 'AND'));
		$results = $qb->getQuery()->getResult();

		if (!empty($results)) {
			return $results;
		}

		# поиск по любому из слов
		$qb->where($this->where($words, 'OR'));
		$results = $qb->getQuery()->getResult();

		if (!empty($results)) {
			return $results;
		}

		return array();
	}

	private function where($words, $s)
	{
		$s = ($s == 'OR') ? ' OR ' : ' AND ';

		$where = '';
		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= $s;
			}
			$where .= "(g.Name LIKE '$word%' OR g.Name LIKE '% $word%')";
		}

		return $where;
	}
}