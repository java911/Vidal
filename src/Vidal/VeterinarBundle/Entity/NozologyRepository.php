<?php

namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\EntityRepository;

class NozologyRepository extends EntityRepository
{
	public function findByCode($code)
	{
		return $this->_em->createQuery('
			SELECT n.NozologyCode, n.Code, n.Name
			FROM VidalVeterinarBundle:Nozology n
			WHERE n.NozologyCode = :code
		')->setParameter('code', $code)
			->getOneOrNullResult();
	}

	public function findNozologyNames()
	{
		$names = array();

		$namesRaw = $this->_em->createQuery('
			SELECT n.Name
			FROM VidalVeterinarBundle:Nozology n
			ORDER BY n.Name ASC
		')->getResult();

		for ($i = 0, $c = count($namesRaw); $i < $c; $i++) {
			$names[] = mb_strtolower($namesRaw[$i]['Name'], 'UTF-8');
		}

		return $names;
	}

	public function findAll()
	{
		return $this->_em->createQuery('
		 	SELECT n.NozologyCode, n.Name
		 	FROM VidalVeterinarBundle:Nozology n
		 	ORDER BY n.Name ASC
		')->getResult();
	}

	public function findByQuery($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select('DISTINCT n.NozologyCode, n.Name')
			->from('VidalVeterinarBundle:Nozology', 'n')
			->orderBy('n.Name', 'ASC');

		# поиск по словам
		$where = '';
		$words = explode(' ', $q);

		# находим все слова
		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= ' AND ';
			}
			$where .= "(n.Name LIKE '$word%' OR n.Name LIKE '% $word%')";
		}

		$qb->where($where);
		$nozologies = $qb->getQuery()->getResult();

		# находим какое-либо из слов, если нет результата
		if (empty($nozologies)) {
			$where = '';
			for ($i = 0; $i < count($words); $i++) {
				$word = $words[$i];
				if ($i > 0) {
					$where .= ' OR ';
				}
				$where .= "(n.Name LIKE '$word%' OR n.Name LIKE '% $word%')";
			}

			$qb->where($where);
			$nozologies = $qb->getQuery()->getResult();
		}

		for ($i=0, $c=count($nozologies); $i<$c; $i++) {
			$nozologies[$i]['Name'] = preg_replace('/' . $q . '/iu', '<span class="query">$0</span>', $nozologies[$i]['Name']);
		}

		return $nozologies;
	}

	public function findByDocumentId($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT n.NozologyCode, n.Code, n.Name
			FROM VidalVeterinarBundle:Nozology n
			JOIN n.documents d WITH d = :DocumentID
			ORDER BY n.Name ASC
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function findByCodes($nozologyCodes)
	{
		return $this->_em->createQuery('
		 	SELECT DISTINCT n.NozologyCode, n.Name
		 	FROM VidalVeterinarBundle:Nozology n
		 	WHERE n.NozologyCode IN (:nozologyCodes)
		')->setParameter('nozologyCodes', $nozologyCodes)
			->getResult();
	}
}