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

	public function findByTagWord($tagId, $text)
	{
		if (empty($text)) {
			return $this->_em->createQuery('
				SELECT p
				FROM VidalDrugBundle:publication p
				JOIN p.tags t WITH t = :tagId
			')->setParameter('tagId', $tagId)
				->getResult();
		}
		else {
			$tagHistory = $this->_em->getRepository('VidalDrugBundle:TagHistory')->findOneByTagText($tagId, $text);
			$ids        = $tagHistory->getPublicationIds();

			if (empty($ids)) {
				return array();
			}

			return $this->_em->createQuery('
				SELECT p
				FROM VidalDrugBundle:Publication p
				WHERE p.id IN (:ids)
			')->setParameter('ids', $ids)
				->getResult();
		}
	}
}