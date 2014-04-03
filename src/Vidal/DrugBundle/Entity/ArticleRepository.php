<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
	public function ofRubrique($rubrique)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE a.rubrique = :rubriqueId
			ORDER BY a.title ASC
		')->setParameter('rubriqueId', $rubrique->getId())
			->getResult();
	}

	public function findLast($top)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE a.enabled = TRUE
			ORDER BY a.date DESC
		')->setMaxResults($top)
			->getResult();
	}

	public function findFrom($from, $max)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Article a
			WHERE a.enabled = TRUE
			ORDER BY a.date DESC
		')->setFirstResult($from)
			->setMaxResults($max)
			->getResult();
	}
}