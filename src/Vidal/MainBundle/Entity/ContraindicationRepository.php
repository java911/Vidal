<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ContraindicationRepository extends EntityRepository
{
	public function findAll()
	{
		return $this->_em->createQuery('
			SELECT c.RusName
			FROM VidalMainBundle:Contraindication c
			ORDER BY c.RusName ASC
		')->getResult();
	}
}