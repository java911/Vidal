<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DiseaseRepository extends EntityRepository
{
    public function findEmpty()
    {
        $result = $this->getEntityManager()->createQuery("
			SELECT d FROM VidalMainBundle:Disease d
            LEFT JOIN VidalMainBundle:DiseaseState ds WITH d MEMBER OF ds.diseases
			WHERE ds.id is NULL ORDER BY d.id ASC
		")->getResult();
        return $result;
    }
}