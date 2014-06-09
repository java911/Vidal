<?php
namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DocumentRepository extends EntityRepository
{
	public function findOneByDocumentID($id)
	{
		return $this->createQueryBuilder('d')
			->select('d')
			->where('d.DocumentID = :id')
			->setParameter('id', $id)
			->getQuery()
			->getOneOrNullResult();
	}

	public function findById($id)
	{
		return $this->createQueryBuilder('d')
			->select('d')
			->where('d.DocumentID = :id')
			->setParameter('id', $id)
			->getQuery()
			->getOneOrNullResult();
	}

	public function findOneByName($name)
	{
		return $this->_em->createQuery('
			SELECT d
			FROM VidalDrugBundle:Document d
			WHERE d.Name = :name
		')->setParameter('name', $name)
			->setMaxResults(1)
			->getOneOrNullResult();
	}

	public function findByName($name)
	{
		# обрезаем расширение после точки и разбиваем по тире
		$pos = strpos($name, '.');
		if ($pos) {
			$name = substr($name, 0, $pos);
		}
		$name  = strtoupper($name);
		$names = explode('-', $name);

		# ищем документ с ArticleID 2,5
		$qb = $this->createQueryBuilder('d')
			->select('d')
			->andWhere('d.ArticleID IN (2,5)')
			->orderBy('d.ArticleID', 'ASC')
			->addOrderBy('d.YearEdition', 'DESC')
			->setMaxResults(1);

		$count = count($names);

		if ($count == 1) {
			$qb->andWhere("d.Name = '{$name}'");
		}
		else {
			for ($i = 0; $i < $count; $i++) {
				$word = $names[$i];
				if ($i == 0) {
					$qb->andWhere("d.Name LIKE '{$word}%'");
				}
				elseif ($i == $count - 1) {
					$qb->andWhere("d.Name LIKE '%{$word}'");
				}
				else {
					$qb->andWhere("d.Name LIKE '%{$word}%'");
				}
			}
		}
		$document = $qb->getQuery()->getOneOrNullResult();

		# ищем документ с ArticleID 4,3,1
		if (!$document) {
			$qb = $this->createQueryBuilder('d')
				->select('d')
				->andWhere('d.ArticleID IN (4,3,1)')
				->orderBy('d.ArticleID', 'DESC')
				->addOrderBy('d.YearEdition', 'DESC')
				->setMaxResults(1);

			if ($count == 1) {
				$qb->andWhere("d.Name = '{$name}'");
			}
			else {
				for ($i = 0; $i < $count; $i++) {
					$word = $names[$i];
					if ($i == 0) {
						$qb->andWhere("d.Name LIKE '{$word}%'");
					}
					elseif ($i == $count - 1) {
						$qb->andWhere("d.Name LIKE '%{$word}'");
					}
					else {
						$qb->andWhere("d.Name LIKE '%{$word}%'");
					}
				}
			}

			$document = $qb->getQuery()->getOneOrNullResult();
		}

		return $document;
	}

	public function findByMoleculeID($MoleculeID)
	{
		return $this->_em->createQuery('
			SELECT d
			FROM VidalDrugBundle:Document d
			LEFT JOIN d.molecules m
			WHERE m.MoleculeID = :MoleculeID
				AND d.ArticleID = 1
			ORDER BY d.YearEdition DESC
		')->setParameter('MoleculeID', $MoleculeID)
			->setMaxResults(1)
			->getOneOrNullResult();
	}

	public function findByNozologyCode($code)
	{
		return $this->_em->createQuery("
			SELECT DISTINCT d.DocumentID, d.ArticleID, d.CountryEditionCode
			FROM VidalDrugBundle:Document d
			JOIN d.nozologies n WITH n.Code = :code
		")->setParameter('code', $code)
			->getResult();
	}

	public function findClPhGroupsByQuery($q)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select('DISTINCT d.ClPhGrName name, d.ClPhGrDescription description')
			->from('VidalDrugBundle:Document', 'd')
			->orderBy('d.ClPhGrName', 'ASC');

		# поиск по словам
		$where = '';
		$words = explode(' ', $q);

		for ($i = 0; $i < count($words); $i++) {
			$word = $words[$i];
			if ($i > 0) {
				$where .= ' OR ';
			}
			$where .= "(d.ClPhGrName LIKE '$word%' OR d.ClPhGrName LIKE '% $word%')";
		}

		$qb->andWhere($where);

		$groups = $qb->getQuery()->getResult();

		for ($i = 0, $c = count($groups); $i < $c; $i++) {
			$groups[$i]['description'] = preg_replace('/' . $q . '/iu', '<span class="query">$0</span>', $groups[$i]['description']);
		}

		return $groups;
	}

	public function findIdsByInfoPageID($InfoPageID)
	{
		$documentsRaw = $this->_em->createQuery('
			SELECT DISTINCT d.DocumentID
			FROM VidalDrugBundle:Document d
			JOIN d.infoPages i
			WHERE i.InfoPageID = :InfoPageID
				AND d.ArticleID IN (2,5,4,3)
			ORDER BY d.DocumentID
		')->setParameter('InfoPageID', $InfoPageID)
			->getResult();

		$documents = array();

		foreach ($documentsRaw as $document) {
			$documents[] = $document['DocumentID'];
		}

		return $documents;
	}

	public function findIdsByNozologyContraCodes($nozologyCodes, $contraCodes)
	{
		$qb = $this->_em->createQueryBuilder();

		$qb->select('DISTINCT d.DocumentID')
			->from('VidalDrugBundle:Document', 'd');

		if (!empty($nozologyCodes)) {
			$qb->join('d.nozologies', 'n', 'WITH', 'n.NozologyCode IN (:nozologyCodes)')
				->setParameter('nozologyCodes', $nozologyCodes);
		}

		$documents = $qb->getQuery()->getResult();
		$documentIds = array();

		for ($i=0, $c=count($documents); $i<$c; $i++) {
			$documentIds[] = $documents[$i]['DocumentID'];
		}

		return $documentIds;
	}

	public function findIndicationsByProductIds($productIds)
	{
		$raw = $this->_em->createQuery('
			SELECT p.ProductID, d.Indication
			FROM VidalDrugBundle:Product p
			LEFT JOIN p.document d
			WHERE p.ProductID IN (:productIds)
		')->setParameter('productIds', $productIds)
			->getResult();

		$indications = array();

		for ($i=0; $i<count($raw); $i++) {
			$key = $raw[$i]['ProductID'];
			$indications[$key] = $raw[$i]['Indication'];
		}

		return $indications;
	}

	public function findOneByText($text)
	{
		$pos = strpos($text, ' ');
		$id = intval(substr($text, 0, $pos));

		return $this->_em->createQuery('
			SELECT d
			FROM VidalDrugBundle:Document d
			WHERE d.DocumentID = :id
		')->setParameter('id', $id)
			->getOneOrNullResult();
	}

	public function findByArticle(Article $article)
	{
		return $this->_em->createQuery('
			SELECT d
			FROM VidalDrugBundle:Document d
			JOIN d.nozologies n
			JOIN n.articles a
			JOIN d.products p
			WHERE a = :articleId
				AND p.NonPrescriptionDrug = TRUE
				AND p.MarketStatusID IN (1,2)
				AND p.ProductTypeCode IN (\'DRUG\',\'GOME\')
				AND d.ArticleID != 4
			GROUP BY d.RusName
			ORDER BY d.RusName ASC
		')->setParameter('articleId', $article->getId())
			->getResult();
	}
}