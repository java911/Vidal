<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class OldArticleRepository extends EntityRepository
{
	public function findByCompanyId($companyId)
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalDrugBundle:OldArticle a
		 	WHERE a.enabled = 1 AND a.oldCompany = :companyId
		 	ORDER BY a.created DESC
		')->setParameter('companyId', $companyId)
			->getResult();
	}
}