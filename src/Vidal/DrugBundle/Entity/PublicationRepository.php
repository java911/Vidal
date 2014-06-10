<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PublicationRepository extends EntityRepository
{
	public function findLast($top)
	{
		return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.date < :now
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
			ORDER BY p.priority DESC, p.date DESC
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
}