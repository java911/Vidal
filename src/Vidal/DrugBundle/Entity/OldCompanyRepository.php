<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class OldCompanyRepository extends EntityRepository
{
	public function findWithArticles()
	{
		return $this->_em->createQuery('
		 	SELECT c
		 	FROM VidalDrugBundle:OldCompany c
		 	WHERE SIZE(c.oldArticles) > 0
		 	ORDER BY c.title ASC
		')->getResult();
	}
}