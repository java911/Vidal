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

	public function findByQuery($q, $badIncluded = false)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb
			->select('p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, p.ProductID,
				p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug,
				d.Indication, d.ArticleID')
			->from('VidalMainBundle:Product', 'p')
			->leftJoin('VidalMainBundle:ProductDocument', 'pd', 'WITH', 'pd.ProductID = p')
			->leftJoin('VidalMainBundle:Document', 'd', 'WITH', 'pd.DocumentID = d')
			->orderBy('p.RusName', 'ASC')
			->addOrderBy('pd.Ranking', 'DESC')
			->andWhere("p.CountryEditionCode = 'RUS'")
			->andWhere('p.MarketStatusID IN (1,2)');

		# включать ли бады
		if ($badIncluded) {
			$qb->andWhere("p.ProductTypeCode IN ('DRUG', 'GOME', 'BAD')");
		}
		else {
			$qb->andWhere("p.ProductTypeCode IN ('DRUG', 'GOME')");
		}

		# поиск по словам
		$where = '';
		$words = explode(' ', $q);

		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= ' OR ';
			}
			$where .= "(p.RusName LIKE '$word%' OR p.EngName LIKE '$word%' OR p.RusName LIKE '% $word%' OR p.EngName LIKE '% $word%')";
		}

		$qb->andWhere($where);

		$productsRaw     = $qb->getQuery()->getResult();
		$products        = array();
		$articlePriority = array(2, 5, 4, 3, 1);

		# отсеиваем дубли препаратов
		for ($i = 0; $i < count($productsRaw); $i++) {
			$key = $productsRaw[$i]['ProductID'];
			if (!isset($products[$key])) {
				$products[$key] = $productsRaw[$i];
			}
			else {
				# надо взять препарат по приоритету Document.ArticleID [2,5,4,3,1]
				$curr = array_search($products[$key]['ArticleID'], $articlePriority);
				$new  = array_search($productsRaw[$i]['ArticleID'], $articlePriority);
				if ($new < $curr) {
					$products[$key] = $productsRaw[$i];
				}
			}
		}

		return array_values($products);
	}

	public function findByDocuments25($documents)
	{
		$documentIds = array();

		//d.CountryEditionCode = 'RUS'
		foreach ($documents as $document) {
			if ($document['CountryEditionCode'] == 'RUS' &&
				($document['ArticleID'] == 2 || $document['ArticleID'] == 5)) {
				$documentIds[] = $document['DocumentID'];
			}
		}

		if (empty($documentIds)) {
			return array();
		}

		$productsRaw = $this->_em->createQuery('
			SELECT p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, ms.RusName MarketStatus, p.ProductID,
				p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug, d.Indication, d.DocumentID
			FROM VidalMainBundle:Product p
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.ProductID = p
			LEFT JOIN VidalMainBundle:Document d WITH pd.DocumentID = d
			LEFT JOIN VidalMainBundle:MarketStatus ms WITH ms.MarketStatusID = p.MarketStatusID
			WHERE d IN (:documentIds) AND
				p.CountryEditionCode = \'RUS\' AND
				(p.MarketStatusID = 1 OR p.MarketStatusID = 2) AND
				(p.ProductTypeCode = \'DRUG\' OR p.ProductTypeCode = \'GOME\')
			ORDER BY pd.Ranking DESC, p.RusName ASC
		')->setParameter('documentIds', $documentIds)
			->getResult();

		# исключаем повторения продуктов по приоритему
		$products = array();

		for ($i = 0, $c = count($productsRaw); $i < $c; $i++) {
			$key = $productsRaw[$i]['ProductID'];

			if (!isset($products[$key])) {
				$products[$key] = $productsRaw[$i];
			}
		}

		return $products;
	}

	public function findByDocuments4($documents)
	{
		$documentIds = array();

		foreach ($documents as $document) {
			if ($document['CountryEditionCode'] == 'RUS' &&
				$document['ArticleID'] == 4) {
				$documentIds[] = $document['DocumentID'];
			}
		}

		if (empty($documentIds)) {
			return array();
		}

		$productsRaw = $this->_em->createQuery('
			SELECT p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, ms.RusName MarketStatus, p.ProductID,
				p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug, d.Indication, d.DocumentID
			FROM VidalMainBundle:Product p
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.ProductID = p
			LEFT JOIN VidalMainBundle:Document d WITH pd.DocumentID = d
			LEFT JOIN VidalMainBundle:MarketStatus ms WITH ms.MarketStatusID = p.MarketStatusID
			WHERE d IN (:documentIds) AND
				p.CountryEditionCode = \'RUS\' AND
				(p.MarketStatusID = 1 OR p.MarketStatusID = 2) AND
				(p.ProductTypeCode = \'DRUG\' OR p.ProductTypeCode = \'GOME\')
			ORDER BY pd.Ranking DESC, p.RusName ASC
		')->setParameter('documentIds', $documentIds)
			->getResult();

		# исключаем повторения продуктов по приоритему
		$products = array();

		for ($i = 0, $c = count($productsRaw); $i < $c; $i++) {
			$key = $productsRaw[$i]['ProductID'];

			if (!isset($products[$key])) {
				$products[$key] = $productsRaw[$i];
			}
		}

		return $products;
	}

	public function findByClPhGroup($description)
	{
		return $this->_em->createQuery('
			SELECT p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, ms.RusName MarketStatus, p.ProductID,
				p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug,
				d.Indication, d.DocumentID, d.ClPhGrDescription
			FROM VidalMainBundle:Product p
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.ProductID = p
			LEFT JOIN VidalMainBundle:Document d WITH pd.DocumentID = d
			LEFT JOIN VidalMainBundle:MarketStatus ms WITH ms.MarketStatusID = p.MarketStatusID
			WHERE d.ClPhGrName = :description AND
				p.CountryEditionCode = \'RUS\' AND
				p.MarketStatusID IN (1,2) AND
				p.ProductTypeCode IN (\'DRUG\',\'GOME\')
			ORDER BY p.RusName ASC
		')->setParameter('description', $description)
			->getResult();
	}

	public function findByPhThGroup($id)
	{
		return $this->_em->createQuery('
			SELECT p.ZipInfo, p.RegistrationNumber, p.RegistrationDate, ms.RusName MarketStatus, p.ProductID,
				p.RusName, p.EngName, p.Name, p.NonPrescriptionDrug,
				d.Indication, d.DocumentID
			FROM VidalMainBundle:Product p
			JOIN p.phthgroups g WITH g.id = :id
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.ProductID = p
			LEFT JOIN VidalMainBundle:Document d WITH pd.DocumentID = d
			LEFT JOIN VidalMainBundle:MarketStatus ms WITH ms.MarketStatusID = p.MarketStatusID
			WHERE p.CountryEditionCode = \'RUS\' AND
				p.MarketStatusID IN (1,2) AND
				p.ProductTypeCode IN (\'DRUG\',\'GOME\')
			ORDER BY p.RusName ASC
		')->setParameter('id', $id)
			->getResult();
	}

	public function findPhThGroupsByQuery($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select('DISTINCT g.Name, g.id')
			->from('VidalMainBundle:Product', 'p')
			->join('p.phthgroups', 'g')
			->where("p.CountryEditionCode = 'RUS' AND
				p.MarketStatusID IN (1,2) AND
				p.ProductTypeCode IN ('DRUG','GOME')")
			->orderBy('g.Name', 'ASC');

		# поиск по словам
		$where = '';
		$words = explode(' ', $q);

		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= ' OR ';
			}
			$where .= "(g.Name LIKE '$word%' OR g.Name LIKE '% $word%')";
		}

		$qb->andWhere($where);

		$groups = $qb->getQuery()->getResult();

		for ($i = 0, $c = count($groups); $i < $c; $i++) {
			$name               = $this->mb_ucfirst($groups[$i]['Name']);
			$groups[$i]['Name'] = preg_replace('/' . $q . '/iu', '<span class="query">$0</span>', $name);
		}

		return $groups;
	}

	/**
	 * Функция возвращает слово с заглавной первой буквой (c поддержкой кирилицы)
	 *
	 * @param string $string
	 * @param string $encoding
	 * @return string
	 */
	private function mb_ucfirst($string, $encoding = 'utf-8')
	{
		$strlen    = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then      = mb_substr($string, 1, $strlen - 1, $encoding);

		return mb_strtoupper($firstChar, $encoding) . $then;
	}
}