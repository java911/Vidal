<?php
namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\EntityRepository;

class InfoPageRepository extends EntityRepository
{
	public function findByInfoPageID($InfoPageID)
	{
		return $this->_em->createQuery('
			SELECT i.InfoPageID, i.RusName, i.RusAddress, c.RusName Country
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			WHERE i = :InfoPageID
		')->setParameter('InfoPageID', $InfoPageID)
			->getOneOrNullResult();
	}

	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT i.InfoPageID, i.RusName, c.RusName Country
			FROM VidalVeterinarBundle:InfoPage i
			LEFT JOIN VidalVeterinarBundle:DocumentInfoPage di WITH di.InfoPageID = i
			LEFT JOIN VidalVeterinarBundle:Country c WITH i.CountryCode = c
			WHERE di.DocumentID = :DocumentID
			ORDER BY di.Ranking DESC
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}
}