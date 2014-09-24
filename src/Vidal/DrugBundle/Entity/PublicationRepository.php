<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PublicationRepository extends EntityRepository
{
	public function findLast($top = 5, $testMode = false)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select('p')
			->from('VidalDrugBundle:Publication', 'p')
			->andWhere('p.date < :now')
			->andWhere('p.priority IS NULL')
			->orderBy('p.date', 'DESC')
			->setParameter('now', new \DateTime())
			->setMaxResults($top);

		$testMode
			? $qb->andWhere('p.enabled = TRUE OR p.testMode = TRUE')
			: $qb->andWhere('p.enabled = TRUE');

		return $qb->getQuery()->getResult();
	}

	public function findLastPriority($top = 3, $testMode = false)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select('p')
			->from('VidalDrugBundle:Publication', 'p')
			->andWhere('p.date < :now')
			->andWhere('p.priority IS NOT NULL')
			->orderBy('p.priority', 'DESC')
			->addOrderBy('p.date', 'DESC')
			->setParameter('now', new \DateTime())
			->setMaxResults($top);

		$testMode
			? $qb->andWhere('p.enabled = TRUE OR p.testMode = TRUE')
			: $qb->andWhere('p.enabled = TRUE');

		return $qb->getQuery()->getResult();
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

	public function getQueryEnabled($testMode = false)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select('p')
			->from('VidalDrugBundle:Publication', 'p')
			->andWhere('p.date < :now')
			->andWhere('p.priority IS NULL')
			->orderBy('p.date', 'DESC')
			->setParameter('now', new \DateTime());

		$testMode
			? $qb->andWhere('p.enabled = TRUE OR p.testMode = TRUE')
			: $qb->andWhere('p.enabled = TRUE');

		return $qb->getQuery();
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

	public function findByTagWord($tag, $text, $partly)
	{
		$tagId = $tag->getId();

		$pdo = $this->_em->getConnection();

		if ($partly === null) {
			$stmt = $pdo->prepare("
				SELECT id
				FROM publication p
				JOIN publication_tag pt ON pt.publication_id = p.id
				WHERE pt.tag_id = $tagId");
		}
		elseif ($partly) {
			$stmt = $pdo->prepare("
				SELECT id
				FROM publication p
				JOIN publication_tag pt ON pt.publication_id = p.id
				WHERE pt.tag_id = $tagId
					AND (p.title LIKE '%{$text}%' OR p.body LIKE '%{$text}%' OR p.announce LIKE '%{$text}%')");
		}
		else {
			$stmt = $pdo->prepare("
				SELECT id
				FROM publication p
				JOIN publication_tag pt ON pt.publication_id = p.id
				WHERE pt.tag_id = $tagId
					AND (p.title REGEXP '[[:<:]]{$text}[[:>:]]' OR p.body REGEXP '[[:<:]]{$text}[[:>:]]' OR p.announce REGEXP '[[:<:]]{$text}[[:>:]]')");
		}

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
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.id IN (:ids)
		')->setParameter('ids', $ids)
			->getResult();
	}
}