<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class SubdivisionRepository extends EntityRepository
{
	public function findVracham()
	{
		return $this->_em->createQuery('
		 	SELECT s
		 	FROM VidalDrugBundle:Subdivision s
		 	WHERE s.parentId = 167
		 	ORDER BY s.name ASC
		')->getResult();
	}
}