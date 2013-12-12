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
			SELECT m.MoleculeID, m.LatName, m.RusName, mnn.GNParent, mnn.description
			FROM VidalMainBundle:Molecule m
			LEFT JOIN VidalMainBundle:MoleculeDocument md WITH md.MoleculeID = m
			LEFT JOIN VidalMainBundle:Document d WITH md.DocumentID = d
			LEFT JOIN VidalMainBundle:MoleculeBase mnn WITH mnn.GNParent = m.GNParent
			WHERE d.DocumentID = :DocumentID
			ORDER BY md.Ranking DESC
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function findByProductID($ProductID)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT m.MoleculeID, m.LatName, m.RusName, mnn.GNParent, mnn.description
			FROM VidalMainBundle:Molecule m
			LEFT JOIN VidalMainBundle:MoleculeName mn WITH mn.MoleculeID = m
			LEFT JOIN mn.products p
			LEFT JOIN VidalMainBundle:MoleculeBase mnn WITH mnn.GNParent = m.GNParent
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

	public function findMoleculeNames()
	{
		$molecules = $this->_em->createQuery('
			SELECT DISTINCT m.RusName, m.LatName
			FROM VidalMainBundle:Molecule m
			ORDER BY m.RusName ASC
		')->getResult();

		$moleculeNames = array();

		for ($i = 0; $i < count($molecules); $i++) {
			$patterns     = array('/<SUP>.*<\/SUP>/', '/<SUB>.*<\/SUB>/');
			$replacements = array('', '');
			$RusName      = preg_replace($patterns, $replacements, $molecules[$i]['RusName']);
			$RusName      = mb_strtolower($RusName, 'UTF-8');
			$LatName      = preg_replace($patterns, $replacements, $molecules[$i]['LatName']);
			$LatName      = mb_strtolower($LatName, 'UTF-8');

			if (!empty($RusName)) {
				$moleculeNames[] = $RusName;
			}

			if (!empty($LatName)) {
				$moleculeNames[] = $LatName;
			}
		}

		return $moleculeNames;
	}

	public function findByQuery($q)
	{
		return $this->_em->createQuery('
			SELECT m.MoleculeID, m.LatName, m.RusName, mnn.GNParent, mnn.description
			FROM VidalMainBundle:Molecule m
			LEFT JOIN m.GNParent mnn
			WHERE m.RusName LIKE :q OR m.LatName LIKE :q
			ORDER BY m.RusName ASC
		')->setParameter('q', $q.'%')
			->getResult();
	}

	public function countComponents($productIds)
	{
		$componentsRaw = $this->_em->createQuery('
			SELECT p.ProductID, COUNT(ms.MoleculeID) molecules
			FROM VidalMainBundle:Product p
			LEFT JOIN p.moleculeNames ms
			WHERE p.ProductID IN (:productIds)
			GROUP BY p.ProductID
		')->setParameter('productIds', $productIds)
			->getResult();

		$components = array();

		for ($i=0; $i<count($componentsRaw); $i++) {
			$key = $componentsRaw[$i]['ProductID'];
			$components[$key] = $componentsRaw[$i]['molecules'];
		}

		return $components;
	}
}