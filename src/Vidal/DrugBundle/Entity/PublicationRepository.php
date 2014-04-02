<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PublicationRepository extends EntityRepository
{
	public function getQueryEnabled()
	{
		return $this->_em->createQueryBuilder()
			->select('p')
			->from('VidalDrugBundle:Publication', 'p')
			->where('p.enabled = 1')
			->orderBy('p.date', 'DESC')
			->getQuery();
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