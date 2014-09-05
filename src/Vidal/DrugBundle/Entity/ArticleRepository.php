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

	public function findLast()
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE a.enabled = TRUE
				AND a.date < :now
				AND a.anons = TRUE
			ORDER BY a.anonsPriority DESC, a.date DESC
		')->setParameter('now', new \DateTime())
			->getResult();
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

	public function findByTagWord($tag, $partly)
	{
		$tagSearch = $tag->getSearch();
		$text      = empty($tagSearch) ? $tag->getText() : $tagSearch;
		$tagId     = $tag->getId();

		$pdo  = $this->_em->getConnection();
		$stmt = $partly
			? $pdo->prepare("
				SELECT id
				FROM article a
				JOIN article_tag a_t ON a_t.article_id = a.id
				WHERE a_t.tag_id = $tagId
				 	AND (a.title REGEXP '[[:<:]]{$text}[[:>:]]' OR a.body REGEXP '[[:<:]]{$text}[[:>:]]' OR a.announce REGEXP '[[:<:]]{$text}[[:>:]]')")
			: $pdo->prepare("
				SELECT id
				FROM article a
				JOIN article_tag a_t ON a_t.article_id = a.id
				WHERE a_t.tag_id = $tagId");

		$stmt->execute();

		$articleIds = array();
		$raw        = $stmt->fetchAll();

		foreach ($raw as $a) {
			$articleIds[] = $a['id'];
		}

		if (empty($articleIds)) {
			return null;
		}

		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE a.id IN (:ids)
		')->setParameter('ids', $articleIds)
			->getResult();
	}
}