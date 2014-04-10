<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AboutRepository extends EntityRepository
{
	public function findTop()
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalMainBundle:About a
		 	WHERE a.enabled = TRUE
		 		AND a.id IN (8,9)
		')->getResult();
	}

	public function findBottom()
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalMainBundle:About a
		 	WHERE a.enabled = TRUE
		 		AND a.id NOT IN (8,9)
		')->getResult();
	}
}