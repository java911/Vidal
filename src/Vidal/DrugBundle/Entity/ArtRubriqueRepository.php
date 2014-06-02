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
			ORDER BY r.priority DESC, r.title ASC
		')->getResult();
	}
}