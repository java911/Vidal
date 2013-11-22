<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MoleculeRepository extends EntityRepository
{
	public function findByMoleculeID($MoleculeID)
	{
		return $this->_em->createQuery('
		 	SELECT m
		 	FROM VidalMainBundle:Molecule m
		 	WHERE m = :MoleculeID
		')->setParameter('MoleculeID', $MoleculeID)
			->getOneOrNullResult();
	}

	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT m.MoleculeID, m.LatName, m.RusName
			FROM VidalMainBundle:Molecule m
			LEFT JOIN VidalMainBundle:MoleculeDocument md WITH md.MoleculeID = m
			LEFT JOIN VidalMainBundle:Document d WITH md.DocumentID = d
			WHERE d.DocumentID = :DocumentID
			ORDER BY md.Ranking DESC
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function findByProductID($ProductID)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT m.MoleculeID, m.LatName, m.RusName
			FROM VidalMainBundle:Molecule m
			LEFT JOIN VidalMainBundle:MoleculeName mn WITH mn.MoleculeID = m
			LEFT JOIN mn.products p
			WHERE p = :ProductID
		')->setParameter('ProductID', $ProductID)
			->getResult();
	}

	public function findOneByProductID($ProductID)
	{
		return $this->_em->createQuery('
			SELECT m.MoleculeID, m.LatName, m.RusName
			FROM VidalMainBundle:Molecule m
			LEFT JOIN VidalMainBundle:MoleculeName mn WITH mn.MoleculeID = m
			LEFT JOIN mn.products p
			WHERE p = :ProductID
		')->setParameter('ProductID', $ProductID)
			->setMaxResults(1)
			->getOneOrNullResult();
	}
}