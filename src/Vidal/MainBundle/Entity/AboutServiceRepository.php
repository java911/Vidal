<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AboutServiceRepository extends EntityRepository
{
	public function findServices()
	{
		return $this->_em->createQuery('
		 	SELECT s
		 	FROM VidalMainBundle:AboutService s
		 	WHERE s.enabled = TRUE
		 	ORDER BY s.priority DESC, s.title DESC
		')->getResult();
	}
}