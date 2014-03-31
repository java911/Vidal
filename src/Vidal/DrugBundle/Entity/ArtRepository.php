<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArtRepository extends EntityRepository
{
	public function getQueryBySubdivision($subId)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.subdivision = :subId
				AND a.enabled = TRUE
			ORDER BY a.date DESC, a.priority DESC
		')->setParameter('subId', $subId);
	}
}