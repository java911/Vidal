<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArtTypeRepository extends EntityRepository
{
	public function rubriqueUrl($rubrique, $url)
	{
		return $this->_em->createQuery('
			SELECT t
			FROM VidalDrugBundle:ArtType t
			WHERE t.enabled = 1
				AND t.rubrique = :rubrique
				AND t.url = :url
		')->setParameter('rubrique', $rubrique->getId())
			->setParameter('url', $url)
			->getOneOrNullResult();
	}
}