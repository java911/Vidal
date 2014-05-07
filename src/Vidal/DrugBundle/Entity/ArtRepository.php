<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArtRepository extends EntityRepository
{
	public function getQueryByRubrique($rubrique)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.rubrique = :id
				AND a.enabled = TRUE
			ORDER BY a.date DESC, a.priority DESC
		')->setParameter('id', $rubrique->getId());
	}

	public function getQueryByType($type)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.type = :id
				AND a.enabled = TRUE
			ORDER BY a.date DESC, a.priority DESC
		')->setParameter('id', $type->getId());
	}

	public function getQueryByCategory($category)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.category = :id
				AND a.enabled = TRUE
			ORDER BY a.date DESC, a.priority DESC
		')->setParameter('id', $category->getId());
	}

	public function atIndex()
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalDrugBundle:Art a
		 	WHERE a.atIndex = TRUE
		 	ORDER BY a.updated DESC
		')->setMaxResults(1)
			->getOneOrNullResult();
	}
}