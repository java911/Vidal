<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PhThGroupsRepository extends EntityRepository
{
	public function findByProductID($ProductID)
	{
		return $this->_em->createQuery('
			SELECT g.Name
			FROM VidalMainBundle:PhThGroups g
			JOIN g.products p WITH p = :ProductID
			ORDER BY g.Name
		')->setParameter('ProductID', $ProductID)
			->getResult();
	}
}