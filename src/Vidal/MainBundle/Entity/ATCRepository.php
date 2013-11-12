<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ATCRepository extends EntityRepository
{
	public function findByDocumentID($DocumentID)
	{
		$atc = $this->_em->createQuery('
			SELECT a
			FROM VidalMainBundle:ATC a
			JOIN a.documents d WITH d = :DocumentID
		')->setMaxResults(1)
			->setParameter('DocumentID', $DocumentID)
			->getOneOrNullResult();

		var_dump($atc); exit;

		return null;
	}
}