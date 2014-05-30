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
		 		AND a.created < CURRENT_TIMESTAMP()
		 	ORDER BY a.created DESC
		')->setParameter('companyId', $companyId)
			->getResult();
	}

	public function getQuery()
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:PharmArticle a
			WHERE a.enabled = TRUE
				AND a.created < CURRENT_TIMESTAMP()
			ORDER BY a.created DESC, a.priority DESC
		');
	}

	public function getQueryOfCompany($id)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:PharmArticle a
			WHERE a.enabled = TRUE
				AND a.company = :id
				AND a.created < CURRENT_TIMESTAMP()
			ORDER BY a.created DESC, a.priority DESC
		')->setParameter('id', $id);
	}
}