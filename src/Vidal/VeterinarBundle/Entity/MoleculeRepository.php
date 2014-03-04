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
		')->setParameter('MoleculeID', $MoleculeID)
			->getOneOrNullResult();
	}

	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT m.MoleculeID, m.LatName, m.RusName, mnn.GNParent, mnn.description
			FROM VidalVeterinarBundle:Molecule m
			LEFT JOIN VidalVeterinarBundle:MoleculeDocument md WITH md.MoleculeID = m
			LEFT JOIN VidalVeterinarBundle:Document d WITH md.DocumentID = d
			LEFT JOIN VidalVeterinarBundle:MoleculeBase mnn WITH mnn.GNParent = m.GNParent
			WHERE d.DocumentID = :DocumentID
			ORDER BY md.Ranking DESC
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function findByProductID($ProductID)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT m.MoleculeID, m.LatName, m.RusName, mnn.GNParent, mnn.description
			FROM VidalVeterinarBundle:Molecule m
			LEFT JOIN VidalVeterinarBundle:MoleculeName mn WITH mn.MoleculeID = m
			LEFT JOIN mn.products p
			LEFT JOIN VidalVeterinarBundle:MoleculeBase mnn WITH mnn.GNParent = m.GNParent
			WHERE p = :ProductID
		')->setParameter('ProductID', $ProductID)
			->getResult();
	}

	public function findOneByProductID($ProductID)
	{
		return $this->_em->createQuery('
			SELECT m.MoleculeID, m.LatName, m.RusName
			FROM VidalVeterinarBundle:Molecule m
			LEFT JOIN VidalVeterinarBundle:MoleculeName mn WITH mn.MoleculeID = m
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
			FROM VidalVeterinarBundle:Molecule m
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
		$qb = $this->_em->createQueryBuilder();

		$qb->select('m.MoleculeID, m.LatName, m.RusName, mnn.GNParent, mnn.description')
			->from('VidalVeterinarBundle:Molecule', 'm')
			->leftJoin('m.GNParent', 'mnn')
			->orderBy('m.LatName', 'ASC');

		# поиск по словам
		$where = '';
		$words = explode(' ', $q);

		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= ' OR ';
			}
			$where .= "(m.RusName LIKE '$word%' OR m.LatName LIKE '$word%' OR m.RusName LIKE '% $word%' OR m.LatName LIKE '% $word%')";
		}

		$qb->where($where);

		return $qb->getQuery()->getResult();
	}

	public function countComponents($productIds)
	{
		$componentsRaw = $this->_em->createQuery('
			SELECT p.ProductID, COUNT(ms.MoleculeID) molecules
			FROM VidalVeterinarBundle:Product p
			LEFT JOIN p.moleculeNames ms
			WHERE p.ProductID IN (:productIds)
			GROUP BY p.ProductID
		')->setParameter('productIds', $productIds)
			->getResult();

		$components = array();

		for ($i = 0; $i < count($componentsRaw); $i++) {
			$key              = $componentsRaw[$i]['ProductID'];
			$components[$key] = $componentsRaw[$i]['molecules'];
		}

		return $components;
	}

	public function findByDocuments1($documents)
	{
		$documentIds = array();

		foreach ($documents as $document) {
			if ($document['ArticleID'] == 1) {
				$documentIds[] = $document['DocumentID'];
			}
		}

		if (empty($documentIds)) {
			return array();
		}

		return $this->_em->createQuery('
			SELECT DISTINCT m.MoleculeID, m.LatName, m.RusName, mnn.GNParent, mnn.description, d.DocumentID
			FROM VidalVeterinarBundle:Molecule m
			JOIN VidalVeterinarBundle:MoleculeDocument md WITH md.MoleculeID = m
			JOIN VidalVeterinarBundle:Document d WITH md.DocumentID = d
			LEFT JOIN m.GNParent mnn
			WHERE d IN (:documentIds)
			ORDER BY m.LatName ASC
		')->setParameter('documentIds', $documentIds)
			->getResult();
	}
}