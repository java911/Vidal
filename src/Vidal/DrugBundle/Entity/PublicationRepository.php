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
				AND p.date < CURRENT_TIMESTAMP()
			ORDER BY p.priority DESC, p.date DESC
		')->setMaxResults($top)
			->getResult();
	}

	public function findFrom($from, $max)
	{
		return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.date < CURRENT_TIMESTAMP()
			ORDER BY p.priority DESC, p.date DESC
		')->setFirstResult($from)
			->setMaxResults($max)
			->getResult();
	}

	public function getQueryEnabled()
	{
		return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
				AND p.date < CURRENT_TIMESTAMP()
			ORDER BY p.priority DESC, p.date DESC
		');
	}
}