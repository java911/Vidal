<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ClPhGroupsRepository extends EntityRepository
{
	public function findOneById($id)
	{
		return $this->_em->createQuery('
			SELECT g
			FROM VidalDrugBundle:ClPhGroups g
			WHERE g = :id
		')->setParameter('id', $id)
			->getOneOrNullResult();
	}

	public function findWithProducts()
	{
		return $this->_em->createQuery('
			SELECT DISTINCT g, COUNT(p) AS HIDDEN total
			FROM VidalDrugBundle:ClPhGroups g
			JOIN g.products p WITH p.ProductTypeCode IN (\'DRUG\', \'GOME\')
			GROUP BY g
			HAVING total > 0
		')->getResult();
	}
}