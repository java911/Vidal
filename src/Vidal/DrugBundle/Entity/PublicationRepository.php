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
			ORDER BY p.date DESC
		')->setMaxResults($top)
			->getResult();
	}

	public function findFrom($from, $max)
	{
		return $this->_em->createQuery('
			SELECT p
			FROM VidalDrugBundle:Publication p
			WHERE p.enabled = TRUE
			ORDER BY p.date DESC
		')->setFirstResult($from)
			->setMaxResults($max)
			->getResult();
	}

	public function getQueryPharm()
	{
		return $this->_em->createQueryBuilder('p')
			->select('p')
			->from('VidalDrugBundle:Publication', 'p')
			->where('p.enabled = 1')
			->orderBy('p.updated', 'DESC')
			->getQuery();
	}
}