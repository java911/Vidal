<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MoleculeRepository extends EntityRepository
{
	public function findByDocumentID($DocumentID)
	{
		return $this->createQueryBuilder('m')
			->select('m')
			->leftJoin('m.moleculeDocuments', 'md', 'WITH', 'md.DocumentID = :DocumentID')
			->setParameter('DocumentID', $DocumentID)
			->orderBy('md.Ranking', 'DESC')
			->setMaxResults(1)
			->getQuery()
			->getSingleResult();
	}
}