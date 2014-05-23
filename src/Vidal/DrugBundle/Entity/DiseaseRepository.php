<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DiseaseRepository extends EntityRepository
{
    public function findEmpty()
    {
        $result = $this->getEntityManager()->createQuery("
			SELECT d FROM VidalAdminBundle:Disease d
            LEFT JOIN VidalAdminBundle:DiseaseState ds WITH d MEMBER OF ds.diseases
			WHERE ds.id is NULL ORDER BY d.id ASC
		")->getResult();
        return $result;
    }
}