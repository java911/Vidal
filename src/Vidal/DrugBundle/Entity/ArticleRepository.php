<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
	public function ofRubrique($rubrique)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE a.rubrique = :rubriqueId
			ORDER BY a.title ASC
		')->setParameter('rubriqueId', $rubrique->getId())
			->getResult();
	}

	public function findLast($top)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE a.enabled = TRUE
				AND a.date < CURRENT_TIMESTAMP()
			ORDER BY a.priority DESC, a.date DESC
		')->setMaxResults($top)
			->getResult();
	}

	public function findFrom($from, $max)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE a.enabled = TRUE
				AND a.date < CURRENT_TIMESTAMP()
			ORDER BY a.priority DESC, a.date DESC
		')->setFirstResult($from)
			->setMaxResults($max)
			->getResult();
	}

	public function findDisease($l)
	{
		return $this->_em->createQuery('
			SELECT a.title, a.synonym, a.link, r.rubrique rubrique
			FROM VidalDrugBundle:Article a
			LEFT JOIN a.rubrique r
			WHERE a.enabled = TRUE
				AND a.date < CURRENT_TIMESTAMP()
				AND (a.title LIKE :l1 OR a.title LIKE :l2 OR a.synonym LIKE :l3 OR a.synonym LIKE :l4)
				AND a.title NOT LIKE :l5
				AND a.synonym NOT LIKE :l6
			ORDER BY a.title ASC
		')->setParameters(array(
				'l1' => $l . '%',
				'l2' => '% ' . $l . '%',
				'l3' => $l . '%',
				'l4' => '% ' . $l . '%',
				'l5' => '% ' . $l . ' %',
				'l6' => '% ' . $l . ' %',
			))
			->getResult();
	}

	public function findByQuery($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select('a.title, a.synonym, a.link, r.rubrique rubrique')
			->from('VidalDrugBundle:Article', 'a')
			->leftJoin('a.rubrique', 'r')
			->where('a.enabled = TRUE')
			->andWhere('r.enabled = TRUE')
			->andWhere('a.date < CURRENT_TIMESTAMP()')
			->andWhere('r.id != 19')
			->orderBy('a.title', 'ASC');

		# поиск по словам
		$where = '';
		$words = explode(' ', $q);

		# находим все слова
		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= ' AND ';
			}
			$where .= "(a.title LIKE '$word%' OR a.title LIKE '% $word%' OR a.synonym LIKE '$word%' OR a.synonym LIKE '% $word%')";
		}

		$qb->andWhere($where);
		$articles = $qb->getQuery()->getResult();

		# находим какое-либо из слов, если нет результата
		if (empty($articles)) {
			$where = '';

			for ($i = 0; $i < count($words); $i++) {
				$word = $words[$i];
				if ($i > 0) {
					$where .= ' OR ';
				}
				$where .= "(a.title LIKE '$word%' OR a.title LIKE '% $word%' OR a.synonym LIKE '$word%' OR a.synonym LIKE '% $word%')";
			}

			$qb->where($where)
				->andWhere('a.enabled = TRUE')
				->andWhere('r.enabled = TRUE')
				->andWhere('a.date < CURRENT_TIMESTAMP()')
				->andWhere('r.id != 19');
			$articles = $qb->getQuery()->getResult();
		}

		return $articles;
	}

	public function findByRubriqueId($id)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE a.enabled = 1
				AND a.rubrique = :id
				AND a.date < CURRENT_TIMESTAMP()
			ORDER BY a.title ASC
		')->setParameter('id', $id)
			->getResult();
	}
}