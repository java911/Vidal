<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArtRubriqueRepository extends EntityRepository
{
	public function findActive()
	{
		return $this->_em->createQuery('
			SELECT r
			FROM VidalDrugBundle:ArtRubrique r
			WHERE r.enabled = TRUE
				AND (SIZE(r.types) > 0 OR SIZE(r.arts) > 0)
			ORDER BY r.title
		')->getResult();
	}
}