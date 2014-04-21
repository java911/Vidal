<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompanyRepository extends EntityRepository
{
	public function findByCompanyID($CompanyID)
	{
		return $this->_em->createQuery('
			SELECT c.CompanyID, c.LocalName CompanyName, c.Property, country.RusName Country
			FROM VidalDrugBundle:Company c
			LEFT JOIN VidalDrugBundle:Country country WITH c.CountryCode = country
			WHERE c = :CompanyID
		')->setParameter('CompanyID', $CompanyID)
			->getOneOrNullResult();
	}

	public function findOwnersByProducts($productIds)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT c.CompanyID, pc.CompanyRusNote, pc.CompanyEngNote, c.LocalName, c.Property,
				country.RusName Country
			FROM VidalDrugBundle:Company c
			LEFT JOIN VidalDrugBundle:ProductCompany pc WITH pc.CompanyID = c
			LEFT JOIN VidalDrugBundle:Country country WITH c.CountryCode = country
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
			FROM VidalDrugBundle:Company c
			LEFT JOIN VidalDrugBundle:ProductCompany pc WITH pc.CompanyID = c
			LEFT JOIN VidalDrugBundle:Country country WITH c.CountryCode = country
			WHERE pc.ProductID IN (:productIds) AND
				pc.ItsMainCompany = 0
			ORDER BY pc.CompanyRusNote ASC
		')->setParameter('productIds', $productIds)
			->getResult();
	}

	public function findByProducts($productIds)
	{
		$companies = $this->_em->createQuery('
			SELECT c.CompanyID, pc.CompanyRusNote, pc.CompanyEngNote, c.LocalName, c.Property,
				country.RusName Country, pc.ItsMainCompany, p.ProductID
			FROM VidalDrugBundle:Company c
			LEFT JOIN VidalDrugBundle:ProductCompany pc WITH pc.CompanyID = c
			LEFT JOIN VidalDrugBundle:Country country WITH c.CountryCode = country
			LEFT JOIN VidalDrugBundle:Product p WITH p = pc.ProductID
			WHERE pc.ProductID IN (:productIds)
			ORDER BY pc.ItsMainCompany DESC
		')->setParameter('productIds', $productIds)
			->getResult();

		$productCompanies = array();

		# надо получить компании и сгруппировать их по продукту
		foreach ($companies as $company) {
			$key = $company['ProductID'];
			isset($productCompanies[$key])
				? $productCompanies[$key][] = $company
				: $productCompanies[$key] = array($company);
		}

		return $productCompanies;
	}

	public function findByQuery($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb
			->select('c.CompanyID, c.LocalName, c.Property, country.RusName Country')
			->from('VidalDrugBundle:Company', 'c')
			->leftJoin('VidalDrugBundle:Country', 'country', 'WITH', 'country.CountryCode = c.CountryCode')
			->orderBy('c.LocalName', 'ASC')
			->where("c.CountryEditionCode = 'RUS'")
			->andWhere("c.countProducts > 0");

		# поиск по всем словам
		$where = '';
		$words = explode(' ', $q);

		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= ' AND ';
			}
			$where .= "(c.LocalName LIKE '$word%' OR c.LocalName LIKE '% $word%')";
		}

		$qb->andWhere($where);
		$results = $qb->getQuery()->getResult();

		# поиск по одному слову
		if (empty($results)) {
			$where = '';
			for ($i = 0; $i < count($words); $i++) {
				$word = $words[$i];
				if ($i > 0) {
					$where .= ' OR ';
				}
				$where .= "(c.LocalName LIKE '$word%' OR c.LocalName LIKE '% $word%')";
			}

			$qb->where("c.CountryEditionCode = 'RUS'")
				->andWhere("c.countProducts > 0")
				->andWhere($where);

			return $qb->getQuery()->getResult();
		}

		return $results;
	}

	public function getQuery()
	{
		return $this->_em->createQuery("
			SELECT c
			FROM VidalDrugBundle:Company c
			WHERE c.CountryEditionCode = 'RUS'
				AND c.countProducts > 0
			ORDER BY c.LocalName ASC
		");
	}

	public function getQueryByLetter($l)
	{
		return $this->_em->createQuery("
			SELECT c
			FROM VidalDrugBundle:Company c
			WHERE c.CountryEditionCode = 'RUS'
				AND c.LocalName LIKE :l
				AND c.countProducts > 0
			ORDER BY c.LocalName ASC
		")->setParameter('l', $l . '%');
	}

	public function findByQueryString($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb
			->select('c')
			->from('VidalDrugBundle:Company', 'c')
			->orderBy('c.LocalName', 'ASC')
			->where("c.CountryEditionCode = 'RUS'")
			->andWhere("c.countProducts > 0");

		# поиск по всем словам
		$where = '';
		$words = explode(' ', $q);

		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= ' AND ';
			}
			$where .= "(c.LocalName LIKE '$word%' OR c.LocalName LIKE '% $word%')";
		}

		$qb->andWhere($where);
		$results = $qb->getQuery()->getResult();

		# поиск по одному слову
		if (empty($results)) {
			$where = '';
			for ($i = 0; $i < count($words); $i++) {
				$word = $words[$i];
				if ($i > 0) {
					$where .= ' OR ';
				}
				$where .= "(c.LocalName LIKE '$word%' OR c.LocalName LIKE '% $word%')";
			}

			$qb->where("c.CountryEditionCode = 'RUS'")
				->andWhere("c.countProducts > 0")
				->andWhere($where);

			return $qb->getQuery()->getResult();
		}

		return $results;
	}

	public function getNames()
	{
		$qb = $this->_em->createQueryBuilder();

		$qb
			->select('c.LocalName')
			->from('VidalDrugBundle:Company', 'c')
			->orderBy('c.LocalName', 'ASC')
			->where("c.CountryEditionCode = 'RUS'");

		$results = $qb->getQuery()->getResult();
		$names   = array();

		foreach ($results as $result) {
			$name = preg_replace('/ &.+; /', ' ', $result['LocalName']);
			$name = preg_replace('/&.+;/', ' ', $name);

			$names[] = $name;
		}

		$uniques = array();

		foreach ($names as $name) {
			if (!isset($uniques[$name])) {
				$uniques[$name] = '';
			}
		}

		return array_keys($uniques);
	}
}