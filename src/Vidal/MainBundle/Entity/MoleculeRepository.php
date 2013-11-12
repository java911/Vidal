<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MoleculeRepository extends EntityRepository
{
	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT m
			FROM VidalMainBundle:Molecule m
			LEFT JOIN VidalMainBundle:MoleculeDocument md WITH md.MoleculeID = m
			LEFT JOIN VidalMainBundle:Document d WITH md.DocumentID = d
			WHERE d.DocumentID = :DocumentID
			ORDER BY md.Ranking DESC
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}
}