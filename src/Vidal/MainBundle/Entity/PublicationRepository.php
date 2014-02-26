<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PublicationRepository extends EntityRepository
{
	public function getQueryEnabled()
	{
		return $this->_em->createQueryBuilder()
			->select('p')
			->from('VidalMainBundle:Publication', 'p')
			->where('p.enabled = 1')
			->orderBy('p.created', 'DESC')
			->getQuery();
	}
}