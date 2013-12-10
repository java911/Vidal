<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompanyRepository extends EntityRepository
{
	public function findByCompanyID($CompanyID)
	{
		return $this->_em->createQuery('
			SELECT c.CompanyID, c.LocalName CompanyName, c.Property, country.RusName Country
			FROM VidalMainBundle:Company c
			LEFT JOIN VidalMainBundle:Country country WITH c.CountryCode = country
			WHERE c = :CompanyID
		')->setParameter('CompanyID', $CompanyID)
			->getOneOrNullResult();
	}

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
			ORDER BY pc.CompanyRusNote ASC
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

	public function findByQuery($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb
			->select('c.CompanyID, c.LocalName, c.Property, country.RusName Country')
			->from('VidalMainBundle:Company', 'c')
			->leftJoin('VidalMainBundle:Country', 'country', 'WITH', 'country.CountryCode = c.CountryCode')
			->orderBy('c.LocalName', 'ASC')
			->andWhere("c.CountryEditionCode = 'RUS'");

		$words = explode(' ', $q);
		$count = count($words);

		if ($count == 1) {
			$qb->andWhere('c.LocalName LIKE :word')->setParameter('word', $q . '%');
		}
		else {
			$where = '';
			for ($i = 0; $i < $count; $i++) {
				$word = $words[$i];
				if ($i == 0) {
					$where .= "c.LocalName LIKE '$word%'";
				}
				else {
					$where .= " AND c.LocalName LIKE '%$word%'";
				}
			}
			$qb->andWhere($where);
		}

		return $qb->getQuery()->getResult();
	}
}