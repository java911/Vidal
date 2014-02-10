<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ContraindicationRepository extends EntityRepository
{
	public function findAll()
	{
		return $this->_em->createQuery('
			SELECT c.RusName, c.ContraIndicCode
			FROM VidalDrugBundle:Contraindication c
			ORDER BY c.RusName ASC
		')->getResult();
	}

	public function findByCodes($contraCodes)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT c.ContraIndicCode, c.RusName
			FROM VidalDrugBundle:Contraindication c
			WHERE c.ContraIndicCode IN (:contraCodes)
		')->setParameter('contraCodes', $contraCodes)
			->getResult();
	}
}