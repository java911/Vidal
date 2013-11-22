<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ATCRepository extends EntityRepository
{
	public function findOneByATCCode($ATCCode)
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalMainBundle:ATC a
		 	WHERE a = :ATCCode
		')->setParameter('ATCCode', $ATCCode)
			->getOneOrNullResult();
	}

	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalMainBundle:ATC a
			JOIN a.documents d WITH d = :DocumentID
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function findByProducts($productIds)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT a
			FROM VidalMainBundle:ATC a
			JOIN a.products p
			WHERE p IN (:productIds)
		')->setParameter('productIds', $productIds)
			->getResult();
	}
}