<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AboutRepository extends EntityRepository
{
	public function findAbout()
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalMainBundle:About a
		 	WHERE a.enabled = TRUE
		 		AND a.id IN (8,9,6)
		 	ORDER BY a.priority DESC, a.title DESC
		')->getResult();
	}

	public function findServices()
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalMainBundle:About a
		 	WHERE a.enabled = TRUE
		 		AND a.id NOT IN (8,9,6)
		 	ORDER BY a.priority DESC, a.title DESC
		')->getResult();
	}
}