<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
	public function ofRubrique($rubrique, $testMode = false)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select('a')
			->from('VidalDrugBundle:Article', 'a')
			->where('a.rubrique = :rubriqueId')
			->orderBy('a.title', 'ASC')
			->setParameter('rubriqueId', $rubrique->getId());

		$testMode
			? $qb->andWhere('a.enabled = TRUE OR a.testMode = TRUE')
			: $qb->andWhere('a.enabled = TRUE');

		return $qb->getQuery()->getResult();
	}

	public function findLast($testMode = false)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select('a')
			->from('VidalDrugBundle:Article', 'a')
			->andWhere('a.date < :now')
			->andWhere('a.anons = TRUE')
			->orderBy('a.anonsPriority', 'DESC')
			->addOrderBy('a.date', 'DESC')
			->setParameter('now', new \DateTime());

		$testMode
			? $qb->andWhere('a.enabled = TRUE OR a.testMode = TRUE')
			: $qb->andWhere('a.enabled = TRUE');

		return $qb->getQuery()->getResult();
	}

	public function findFrom($from, $max)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE a.enabled = TRUE
				AND a.date < :now
			ORDER BY a.priority DESC, a.date DESC
		')->setParameter('now', new \DateTime())
			->setFirstResult($from)
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
				AND r.enabled = TRUE
				AND a.date < :now
				AND (a.title LIKE :l1 OR a.title LIKE :l2 OR a.synonym LIKE :l3 OR a.synonym LIKE :l4)
				AND a.title NOT LIKE :l5
				AND a.synonym NOT LIKE :l6
			ORDER BY a.title ASC
		')->setParameters(array(
			'now' => new \DateTime(),
			'l1'  => $l . '%',
			'l2'  => '% ' . $l . '%',
			'l3'  => $l . '%',
			'l4'  => '% ' . $l . '%',
			'l5'  => '% ' . $l . ' %',
			'l6'  => '% ' . $l . ' %',
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
			->andWhere('a.date < :now')
			->setParameter('now', new \DateTime())
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
				$where .= "(a.title LIKE '$word%' OR a.title LIKE '% $word%' OR a.synonym LIKE '$word%' OR a.synonym LIKE '% $word%')";
			}

			$qb->where($where)
				->andWhere('a.enabled = TRUE')
				->andWhere('r.enabled = TRUE')
				->andWhere('a.date < :now')
				->setParameter('now', new \DateTime());
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
				AND a.date < :now
			ORDER BY a.title ASC
		')->setParameter('now', new \DateTime())
			->setParameter('id', $id)
			->getResult();
	}

	public function getQueryByTag($tagId)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			JOIN a.tags t
			WHERE a.enabled = 1
				AND a.date < :now
				AND t = :tagId
			ORDER BY a.title ASC
		')->setParameter('now', new \DateTime())
			->setParameter('tagId', $tagId)
			->getResult();
	}

	public function findByTagWord($tagId, $text)
	{
		if (empty($text)) {
			$results1 = $this->_em->createQuery('
				SELECT a
				FROM VidalDrugBundle:Article a
				JOIN a.tags t WITH t = :tagId
			')->setParameter('tagId', $tagId)
				->getResult();

			$results2 = $this->_em->createQuery('
				SELECT a
				FROM VidalDrugBundle:Article a
				JOIN a.infoPages i
				JOIN i.tag t WITH t = :tagId
			')->setParameter('tagId', $tagId)
				->getResult();

			$results = array();

			foreach ($results1 as $r) {
				$key           = $r->getId();
				$results[$key] = $r;
			}
			foreach ($results2 as $r) {
				$key = $r->getId();
				if (!isset($results[$key])) {
					$results[$key] = $r;
				}
			}

			return array_values($results);
		}
		else {
			$tagHistory = $this->_em->getRepository('VidalDrugBundle:TagHistory')->findOneByTagText($tagId, $text);
			$ids        = $tagHistory->getArticleIds();

			if (empty($ids)) {
				return array();
			}

			return $this->_em->createQuery('
				SELECT a
				FROM VidalDrugBundle:Article a
				WHERE a.id IN (:ids)
			')->setParameter('ids', $ids)
				->getResult();
		}
	}

	public function findByNozology($NozologyCode, $MainCode)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			JOIN a.nozologies n
			JOIN a.rubrique r
			WHERE (n.NozologyCode = :NozologyCode OR (n.Code LIKE :MainCode AND n.Level = 0))
				AND a.enabled = TRUE
				AND r.enabled = TRUE
		')->setParameter('NozologyCode', $NozologyCode)
			->setParameter('MainCode', $MainCode . '%')
			->getResult();
	}
}