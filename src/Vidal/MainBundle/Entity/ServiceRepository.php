<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ServiceRepository extends EntityRepository
{
	public function findServices()
	{
		return $this->_em->createQuery('
		 	SELECT s
		 	FROM VidalMainBundle:Service s
		 	WHERE s.enabled = TRUE
		 	ORDER BY a.priority DESC, a.title DESC
		')->getResult();
	}
}