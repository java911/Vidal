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
		# обрезаем расширение .htm
		$name     = strstr($name, '.', true);
		$name     = strtoupper($name);
		$exploded = explode('-', $name);

		if (count($exploded) > 1) {
			$names = array(implode($exploded, ' '));
			$exploded[0] .= '<SUP>&reg;</SUP>';
			array_unshift($names, implode($exploded, ' '));
		}
		else {
			$names = array($name);
		}

		$qb = $this->createQueryBuilder('d')
			->select('d');

		foreach ($names as $n) {
			$qb->orWhere("d.EngName = '{$n}'");
		}

		$qb->orderBy('d.YearEdition', 'DESC')
			->setMaxResults(1);

		return $qb->getQuery()->getOneOrNullResult();
	}

	public function findByProductDocument($ProductID)
	{
		return $this->_em->createQuery('
			SELECT d
			FROM VidalMainBundle:Document d
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.DocumentID = d
			WHERE pd.ProductID = :ProductID AND
				(d.ArticleID = 2 OR d.ArticleID = 5 OR d.ArticleID = 4 OR d.ArticleID = 3)
		')->setParameter('ProductID', $ProductID)
			->setMaxResults(1)
			->getOneOrNullResult();
	}

	public function findByMoleculeID($MoleculeID)
	{

	}
}