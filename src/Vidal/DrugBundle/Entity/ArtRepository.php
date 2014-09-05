<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArtRepository extends EntityRepository
{
	public function getQueryByRubrique($rubrique)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.rubrique = :id
				AND a.type IS NULL
				AND a.enabled = TRUE
				AND a.date < :now
			ORDER BY a.date DESC, a.priority DESC
		')->setParameter('now', new \DateTime())
			->setParameter('id', $rubrique->getId());
	}

	public function getQueryByType($type)
	{
		if (!$type) {
			return null;
		}

		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.type = :id
				AND a.category IS NULL
				AND a.enabled = TRUE
				AND a.date < :now
			ORDER BY a.date DESC, a.priority DESC
		')->setParameter('now', new \DateTime())
			->setParameter('id', $type->getId());
	}

	public function getQueryByCategory($category)
	{
		if (!$category) {
			return null;
		}

		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.category = :id
				AND a.enabled = TRUE
				AND a.date < :now
			ORDER BY a.date DESC, a.priority DESC
		')->setParameter('now', new \DateTime())
			->setParameter('id', $category->getId());
	}

	public function atIndex()
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalDrugBundle:Art a
		 	WHERE a.atIndex = TRUE
		 		AND a.date < :now
		 	ORDER BY a.date DESC
		')->setParameter('now', new \DateTime())
			->setMaxResults(1)
			->getOneOrNullResult();
	}

	public function findForAnons()
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.anons = TRUE
				AND a.enabled = TRUE
				AND a.date < :now
			ORDER BY a.anonsPriority DESC, a.date DESC
		')->setParameter('now', new \DateTime())
			->getResult();
	}

	public function getQueryByTag($tagId)
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalDrugBundle:Art a
		 	JOIN a.tags t
		 	WHERE a.date < :now
		 		AND t = :tagId
		 	ORDER BY a.date DESC
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
				FROM art a
				JOIN art_tag a_t ON a_t.art_id = a.id
				WHERE a_t.tag_id = $tagId
					AND (a.title REGEXP '[[:<:]]{$text}[[:>:]]' OR a.body REGEXP '[[:<:]]{$text}[[:>:]]' OR a.announce REGEXP '[[:<:]]{$text}[[:>:]]')")
			: $pdo->prepare("
				SELECT id
				FROM art a
				JOIN art_tag a_t ON a_t.art_id = a.id
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
			FROM VidalDrugBundle:Art a
			WHERE a.id IN (:ids)
		')->setParameter('ids', $articleIds)
			->getResult();
	}
}