<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompanyRepository extends EntityRepository
{
	public function findOwnersByProducts($productIds)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT c.CompanyID, pc.CompanyRusNote, pc.CompanyEngNote, c.LocalName, c.Property,
				country.RusName Country
			FROM VidalMainBundle:Company c
			LEFT JOIN VidalMainBundle:ProductCompany pc WITH pc.CompanyID = c
			LEFT JOIN VidalMainBundle:Country country WITH c.CountryCode = country
			WHERE pc.ProductID IN (:productIds) AND
				pc.ItsMainCompany = 1
		')->setParameter('productIds', $productIds)
			->getResult();
	}

	public function findDistributorsByProducts($productIds)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT c.CompanyID, pc.CompanyRusNote, pc.CompanyEngNote, c.LocalName, c.Property,
				country.RusName Country
			FROM VidalMainBundle:Company c
			LEFT JOIN VidalMainBundle:ProductCompany pc WITH pc.CompanyID = c
			LEFT JOIN VidalMainBundle:Country country WITH c.CountryCode = country
			WHERE pc.ProductID IN (:productIds) AND
				pc.ItsMainCompany = 0
		')->setParameter('productIds', $productIds)
			->getResult();
	}

	public function findByProducts($productIds)
	{
		return $this->_em->createQuery('
			SELECT c.CompanyID, pc.CompanyRusNote, pc.CompanyEngNote, c.LocalName, c.Property,
				country.RusName Country, pc.ItsMainCompany, p.ProductID
			FROM VidalMainBundle:Company c
			LEFT JOIN VidalMainBundle:ProductCompany pc WITH pc.CompanyID = c
			LEFT JOIN VidalMainBundle:Country country WITH c.CountryCode = country
			LEFT JOIN VidalMainBundle:Product p WITH p = pc.ProductID
			WHERE pc.ProductID IN (:productIds)
			ORDER BY pc.ItsMainCompany DESC
		')->setParameter('productIds', $productIds)
			->getResult();
	}
}