<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProductRepository extends EntityRepository
{
	public function findByProductID($ProductID)
	{
		return $this->_em->createQuery('
			SELECT p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, ms.RusName MarketStatus, p.ProductID,
				p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug, p.Composition
			FROM VidalMainBundle:Product p
			LEFT JOIN VidalMainBundle:MarketStatus ms WITH ms.MarketStatusID = p.MarketStatusID
			WHERE p = :ProductID
		')->setParameter('ProductID', $ProductID)
			->getOneOrNullresult();
	}

	public function findByDocumentID($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, ms.RusName MarketStatus, p.ProductID,
				p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug
			FROM VidalMainBundle:Product p
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.ProductID = p
			LEFT JOIN VidalMainBundle:Document d WITH pd.DocumentID = d
			LEFT JOIN VidalMainBundle:MarketStatus ms WITH ms.MarketStatusID = p.MarketStatusID
			WHERE d = :DocumentID AND
				p.CountryEditionCode = \'RUS\' AND
				(p.MarketStatusID = 1 OR p.MarketStatusID = 2) AND
				(p.ProductTypeCode = \'DRUG\' OR p.ProductTypeCode = \'GOME\')
			ORDER BY pd.Ranking DESC, p.RusName ASC
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}

	public function findByMolecules($molecules)
	{
		$moleculeIds = array();
		foreach ($molecules as $molecule) {
			$moleculeIds[] = $molecule['MoleculeID'];
		}

		return $this->_em->createQuery('
			SELECT p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, ms.RusName MarketStatus, p.ProductID,
				p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug
			FROM VidalMainBundle:Product p
			LEFT JOIN p.moleculeNames mn
			LEFT JOIN VidalMainBundle:Molecule m WITH m = mn.MoleculeID
			LEFT JOIN VidalMainBundle:MarketStatus ms WITH ms.MarketStatusID = p.MarketStatusID
			WHERE m IN (:moleculeIds) AND
				p.CountryEditionCode = \'RUS\' AND
				(p.MarketStatusID = 1 OR p.MarketStatusID = 2) AND
				(p.ProductTypeCode = \'DRUG\' OR p.ProductTypeCode = \'GOME\')
			ORDER BY p.RusName ASC
		')->setParameter('moleculeIds', $moleculeIds)
			->getResult();
	}

	public function findByATCCode($ATCCode)
	{
		return $this->_em->createQuery('
			SELECT p.ProductID, p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, p.NonPrescriptionDrug,
				p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug,
				d.Indication, d.DocumentID, d.ArticleID, d.RusName DocumentRusName, d.EngName DocumentEngName,
				d.Name DocumentName
			FROM VidalMainBundle:Product p
			JOIN p.atcCodes a WITH a = :ATCCode
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.ProductID = p
			LEFT JOIN VidalMainBundle:Document d WITH pd.DocumentID = d
			WHERE p.CountryEditionCode = \'RUS\' AND
				(p.MarketStatusID = 1 OR p.MarketStatusID = 2) AND
				(p.ProductTypeCode = \'DRUG\' OR p.ProductTypeCode = \'GOME\')
			ORDER BY p.RusName ASC
		')->setParameter('ATCCode', $ATCCode)
			->getResult();
	}

	public function findByMoleculeID($MoleculeID)
	{
		return $this->_em->createQuery('
			SELECT p.ZipInfo, p.ProductID, p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug,
				p.RegistrationNumber, p.RegistrationDate,
				d.Indication, d.DocumentID, d.ArticleID, d.RusName DocumentRusName, d.EngName DocumentEngName,
				d.Name DocumentName
			FROM VidalMainBundle:Product p
			LEFT JOIN p.moleculeNames mn
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.ProductID = p
			LEFT JOIN VidalMainBundle:Document d WITH pd.DocumentID = d
			WHERE mn.MoleculeID = :MoleculeID AND
				p.CountryEditionCode = \'RUS\' AND
				(p.MarketStatusID = 1 OR p.MarketStatusID = 2) AND
				(p.ProductTypeCode = \'DRUG\' OR p.ProductTypeCode = \'GOME\')
			ORDER BY d.ArticleID ASC
		')->setParameter('MoleculeID', $MoleculeID)
			->getResult();
	}

	public function findByOwner($CompanyID)
	{
		return $this->_em->createQuery('
			SELECT p.ZipInfo, p.ProductID, p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug,
				p.RegistrationNumber, p.RegistrationDate,
				country.RusName CompanyCountry,
				d.Indication, d.DocumentID, d.ArticleID, d.RusName DocumentRusName, d.EngName DocumentEngName,
				d.Name DocumentName,
				i.InfoPageID, i.RusName InfoPageName, co.RusName InfoPageCountry
			FROM VidalMainBundle:Product p
			JOIN VidalMainBundle:ProductCompany pc WITH pc.ProductID = p
			JOIN VidalMainBundle:Company c WITH pc.CompanyID = c
			LEFT JOIN VidalMainBundle:Country country WITH c.CountryCode = country
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.ProductID = p
			LEFT JOIN VidalMainBundle:Document d WITH pd.DocumentID = d
			LEFT JOIN VidalMainBundle:DocumentInfoPage di WITH di.DocumentID = d
			LEFT JOIN VidalMainBundle:InfoPage i WITH di.InfoPageID = i
			LEFT JOIN VidalMainBundle:Country co WITH i.CountryCode = co
			WHERE c = :CompanyID AND
				p.CountryEditionCode = \'RUS\' AND
				(p.MarketStatusID = 1 OR p.MarketStatusID = 2) AND
				(p.ProductTypeCode = \'DRUG\' OR p.ProductTypeCode = \'GOME\')
			ORDER BY p.RusName ASC
		')->setParameter('CompanyID', $CompanyID)
			->getResult();
	}

	public function findByInfoPageID($InfoPageID)
	{
		return $this->_em->createQuery('
			SELECT p.ZipInfo, p.ProductID, p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug,
				p.RegistrationNumber, p.RegistrationDate,
				d.Indication, d.DocumentID, d.ArticleID, d.RusName DocumentRusName, d.EngName DocumentEngName,
				d.Name DocumentName, d.ClPhGrDescription
			FROM VidalMainBundle:Product p
			JOIN VidalMainBundle:ProductDocument pd WITH pd.ProductID = p
			JOIN VidalMainBundle:Document d WITH pd.DocumentID = d
			JOIN VidalMainBundle:DocumentInfoPage di WITH di.DocumentID = d AND di.InfoPageID = :InfoPageID
			WHERE p.CountryEditionCode = \'RUS\' AND
				(p.MarketStatusID = 1 OR p.MarketStatusID = 2) AND
				(p.ProductTypeCode = \'DRUG\' OR p.ProductTypeCode = \'GOME\')
			ORDER BY p.RusName ASC
		')->setParameter('InfoPageID', $InfoPageID)
			->getResult();
	}

	public function findProductNames()
	{
		$products = $this->_em->createQuery('
			SELECT DISTINCT p.RusName, p.EngName
			FROM VidalMainBundle:Product p
			WHERE p.CountryEditionCode = \'RUS\' AND
				(p.MarketStatusID = 1 OR p.MarketStatusID = 2) AND
				(p.ProductTypeCode = \'DRUG\' OR p.ProductTypeCode = \'GOME\')
			ORDER BY p.RusName ASC
		')->getResult();

		$productNames = array();

		for ($i = 0; $i < count($products); $i++) {
			$patterns     = array('/<SUP>.*<\/SUP>/', '/<SUB>.*<\/SUB>/');
			$replacements = array('', '');
			$RusName      = preg_replace($patterns, $replacements, $products[$i]['RusName']);
			$RusName      = mb_strtolower($RusName, 'UTF-8');
			$EngName      = preg_replace($patterns, $replacements, $products[$i]['EngName']);
			$EngName      = mb_strtolower($EngName, 'UTF-8');

			if (!empty($RusName)) {
				$productNames[] = $RusName;
			}

			if (!empty($EngName)) {
				$productNames[] = $EngName;
			}
		}

		return $productNames;
	}

	public function findByQuery($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb
			->select('p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, p.ProductID,
				p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug')
			->from('VidalMainBundle:Product', 'p')
			->orderBy('p.RusName', 'ASC')
			->andWhere("p.CountryEditionCode = 'RUS'")
			->andWhere('p.MarketStatusID IN (1,2)')
			->andWhere("p.ProductTypeCode IN ('DRUG', 'GOME')");

		$words = explode(' ', $q);
		$count = count($words);

		if ($count == 1) {
			# поиск по единственному слову
			$qb->andWhere('p.RusName LIKE :word OR p.EngName LIKE :word')->setParameter('word', $q . '%');
		}
		else {
			# составной поиск
			$where = '';
			for ($i = 0; $i < $count; $i++) {
				$word = $words[$i];
				if ($i == 0) {
					$where .= "(p.RusName LIKE '$word%' OR p.RusName LIKE '$word%')";
				}
				else {
					$where .= " AND (p.RusName LIKE '%$word%' OR p.EngName LIKE '%$word%')";
				}
			}
			$qb->andWhere($where);
		}

		return $qb->getQuery()->getResult();
	}
}