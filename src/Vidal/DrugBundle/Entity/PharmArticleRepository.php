<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PharmArticleRepository extends EntityRepository
{
	public function findByCompanyId($companyId)
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalDrugBundle:PharmArticle a
		 	WHERE a.enabled = TRUE
		 		AND a.company = :companyId
		 	ORDER BY a.created DESC
		')->setParameter('companyId', $companyId)
			->getResult();
	}
}