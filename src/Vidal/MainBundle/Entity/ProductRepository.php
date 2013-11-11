<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
	public function findDrugsByMoleculeID($MoleculeID)
	{
		return $this->_em->createQuery('
			SELECT p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, ms.RusName MarketStatus, p.ProductID, p.RusName
			FROM VidalMainBundle:Product p
			JOIN p.moleculeNames mn WITH mn.MoleculeID = :MoleculeID
			LEFT JOIN VidalMainBundle:MarketStatus ms WITH ms.MarketStatusID = p.MarketStatusID
			WHERE p.ProductTypeCode = \'DRUG\'
		')->setParameter('MoleculeID', $MoleculeID)
			->getResult();
	}
}