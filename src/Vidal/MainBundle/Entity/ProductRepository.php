<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, ms.RusName MarketStatus, p.ProductID,
				p.RusName, p.EngName, p.NonPrescriptionDrug
			FROM VidalMainBundle:Product p
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.ProductID = p
			LEFT JOIN VidalMainBundle:Document d WITH pd.DocumentID = d
			LEFT JOIN VidalMainBundle:MarketStatus ms WITH ms.MarketStatusID = p.MarketStatusID
			WHERE d = :DocumentID AND
				p.CountryEditionCode = \'RUS\' AND
				(p.MarketStatusID = 1 OR p.MarketStatusID = 2) AND
				(p.ProductTypeCode = \'DRUG\' OR p.ProductTypeCode = \'GOME\')
			ORDER BY pd.Ranking DESC
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}
}