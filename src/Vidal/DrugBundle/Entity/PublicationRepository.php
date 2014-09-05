<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PublicationRepository extends EntityRepository
{
	public function findLast($top = 5)
	{
		return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.date < :now
				AND p.priority IS NULL
			ORDER BY p.date DESC
		')->setParameter('now', new \DateTime())
			->setMaxResults($top)
			->getResult();
	}

	public function findLastPriority($top = 3)
	{
		return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.date < :now
				AND p.priority IS NOT NULL
			ORDER BY p.priority DESC, p.date DESC
		')->setParameter('now', new \DateTime())
			->setMaxResults($top)
			->getResult();
	}

	public function findFrom($from, $max)
	{
		return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.date < :now
			ORDER BY p.priority DESC, p.date DESC
		')->setParameter('now', new \DateTime())
			->setFirstResult($from)
			->setMaxResults($max)
			->getResult();
	}

	public function getQueryEnabled()
	{
		return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.date < :now
				AND p.priority IS NULL
			ORDER BY p.date DESC
		')->setParameter('now', new \DateTime());
	}

	public function getQueryByTag($tagId)
	{
		return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			JOIN p.tags t
			WHERE p.enabled = TRUE
				AND p.date < :now
				AND t = :tagId
			ORDER BY p.priority DESC, p.date DESC
		')->setParameter('now', new \DateTime())
			->setParameter('tagId', $tagId);
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
				FROM publication p
				JOIN publication_tag pt ON pt.publication_id = p.id
				WHERE pt.tag_id = $tagId
					AND (p.title REGEXP '[[:<:]]{$text}[[:>:]]' OR p.body REGEXP '[[:<:]]{$text}[[:>:]]' OR p.announce REGEXP '[[:<:]]{$text}[[:>:]]')")
			: $pdo->prepare("
				SELECT id
				FROM publication p
				JOIN publication_tag pt ON pt.publication_id = p.id
				WHERE pt.tag_id = $tagId");
		$stmt->execute();

		$ids = array();
		$raw = $stmt->fetchAll();

		foreach ($raw as $r) {
			$ids[] = $r['id'];
		}

		if (empty($ids)) {
			return null;
		}

		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Publication a
			WHERE a.id IN (:ids)
		')->setParameter('ids', $ids)
			->getResult();
	}
}