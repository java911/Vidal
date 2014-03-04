<?php

namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArticleRepository extends EntityRepository
{
	public function ofRubrique($rubrique)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalVeterinarBundle:Article a
			WHERE a.rubrique = :rubriqueId
		')->setParameter('rubriqueId', $rubrique->getId())
			->getResult();
	}
}