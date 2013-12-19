<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PhThGroupsRepository extends EntityRepository
{
	public function findById($id)
	{
		return $this->_em->createQuery('
			SELECT g.Name, g.id
			FROM VidalMainBundle:PhThGroups g
			WHERE g = :id
		')->setParameter('id', $id)
			->getOneOrNullResult();
	}

	public function findByProductID($ProductID)
	{
		return $this->_em->createQuery('
			SELECT g.Name, g.id
			FROM VidalMainBundle:PhThGroups g
			JOIN g.products p WITH p = :ProductID
			ORDER BY g.Name
		')->setParameter('ProductID', $ProductID)
			->getResult();
	}
}