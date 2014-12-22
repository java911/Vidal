<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MapCoordRepository extends EntityRepository
{
	 public function byRegion($regionId)
	 {
	 	return $this->_em->createQuery('
			SELECT c.latitude, c.longitude
			FROM VidalMainBundle:MapCoord c
			WHERE c.region = :regionId
		')->setParameter('regionId', $regionId)
			->getResult();
	 }
}