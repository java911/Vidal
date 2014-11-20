<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AstrazenecaFaqRepository extends EntityRepository
{
	public function findAll()
	{
		return $this->_em->createQuery('
		 	SELECT f
		 	FROM VidalMainBundle:AstrazenecaFaq f
		 	ORDER BY f.created DESC
		');
	}
}