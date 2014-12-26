<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MoleculeRepository extends EntityRepository
{
	public function findByMoleculeID($MoleculeID)
	{
		return $this->_em->createQuery('
		 	SELECT m
		 	FROM VidalVeterinarBundle:Molecule m
		 	WHERE m = :MoleculeID
		 		AND m.MoleculeID NOT IN (1144, 2203)
		')->setParameter('MoleculeID', $MoleculeID)
			->getOneOrNullResult();
	}

	public function findByProductID($ProductID)
	{
		$molecules = $this->_em->createQuery('
			SELECT m
			FROM VidalVeterinarBundle:Molecule m
			JOIN m.moleculeNames mn
			JOIN mn.products p
			WHERE p.ProductID = :ProductID
		')->setParameter('ProductID', $ProductID)
			->getResult();

		# если веществ больше 3, то их не отображают
		if (count($molecules) > 3) {
			return array();
		}

		# если среди них хотя бы одно запрещенное - не отображают
		foreach ($molecules as $molecule) {
			if (in_array($molecule->getMoleculeID(), array(1144, 2203))) {
				return array();
			}
		}

		return $molecules;
	}
}