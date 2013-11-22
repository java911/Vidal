<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DocumentRepository extends EntityRepository
{
	public function findById($id)
	{
		return $this->createQueryBuilder('d')
			->select('d')
			->where('d.DocumentID = :id')
			->setParameter('id', $id)
			->getQuery()
			->getOneOrNullResult();
	}

	public function findByName($name)
	{
		# обрезаем расширение после точки и разбиваем по тире
		$name  = preg_replace('/\.*/', '', $name);
		$name  = strtoupper($name);
		$names = explode('-', $name);

		# ищем документ с ArticleID 2,5
		$qb = $this->createQueryBuilder('d')
			->select('d')
			->andWhere('d.ArticleID IN (2,5)')
			->andWhere("d.CountryEditionCode = 'RUS'")
			->orderBy('d.ArticleID', 'ASC')
			->addOrderBy('d.YearEdition', 'DESC')
			->setMaxResults(1);

		$count = count($names);

		if ($count == 1) {
			$qb->andWhere("d.EngName = '{$name}'");
		}
		else {
			for ($i = 0; $i < $count; $i++) {
				$word = $names[$i];
				if ($i == 0) {
					$qb->andWhere("d.EngName LIKE '{$word}%'");
				}
				elseif ($i == $count - 1) {
					$qb->andWhere("d.EngName LIKE '%{$word}'");
				}
				else {
					$qb->andWhere("d.EngName LIKE '%{$word}%'");
				}
			}
		}
		$document = $qb->getQuery()->getOneOrNullResult();

		# ищем документ с ArticleID 4,3,1
		if (!$document) {
			$qb = $this->createQueryBuilder('d')
				->select('d')
				->andWhere('d.ArticleID IN (4,3,1)')
				->andWhere("d.CountryEditionCode = 'RUS'")
				->orderBy('d.ArticleID', 'DESC')
				->addOrderBy('d.YearEdition', 'DESC')
				->setMaxResults(1);

			if ($count == 1) {
				$qb->andWhere("d.EngName = '{$name}'");
			}
			else {
				for ($i = 0; $i < $count; $i++) {
					$word = $names[$i];
					if ($i == 0) {
						$qb->andWhere("d.EngName LIKE '{$word}%'");
					}
					elseif ($i == $count - 1) {
						$qb->andWhere("d.EngName LIKE '%{$word}'");
					}
					else {
						$qb->andWhere("d.EngName LIKE '%{$word}%'");
					}
				}
			}

			$document = $qb->getQuery()->getOneOrNullResult();
		}

		return $document;
	}

	public function findByProductDocument($ProductID)
	{
		$document = $this->_em->createQuery('
			SELECT d
			FROM VidalMainBundle:Document d
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.DocumentID = d
			WHERE pd.ProductID = :ProductID AND d.ArticleID IN (2,5)
			ORDER BY d.ArticleID ASC
		')->setParameter('ProductID', $ProductID)
			->setMaxResults(1)
			->getOneOrNullResult();

		if (!$document) {
			$document = $this->_em->createQuery('
				SELECT d
				FROM VidalMainBundle:Document d
				LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.DocumentID = d
				WHERE pd.ProductID = :ProductID AND d.ArticleID IN (4,3,1)
				ORDER BY d.ArticleID DESC
			')->setParameter('ProductID', $ProductID)
				->setMaxResults(1)
				->getOneOrNullResult();
		}

		return $document;
	}

	public function findByMoleculeID($MoleculeID)
	{
		return $this->_em->createQuery('
			SELECT d
			FROM VidalMainBundle:Document d
			LEFT JOIN VidalMainBundle:MoleculeDocument md WITH md.DocumentID = d
			WHERE md.MoleculeID = :MoleculeID AND d.ArticleID = 1
			ORDER BY d.YearEdition DESC
		')->setParameter('MoleculeID', $MoleculeID)
			->setMaxResults(1)
			->getOneOrNullResult();
	}
}