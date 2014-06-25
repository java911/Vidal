<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ClinicoPhPointersRepository extends EntityRepository
{
	public function findForTree()
	{
		return $this->_em->createQuery('
		 	SELECT c.Name as text, c.Code as id
		 	FROM VidalDrugBundle:ClinicoPhPointers c
		 	WHERE c.Level = 0
		 	ORDER BY c.Code ASC
		')->getResult();
	}

	public function jsonForTree()
	{
		$results = $this->_em->createQuery('
		 	SELECT c.Name as text, c.Code as id, c.countProducts
		 	FROM VidalDrugBundle:ClinicoPhPointers c
		 	ORDER BY c.Code ASC
		')->getResult();

		$codes = array();

		foreach ($results as $code) {
			$key         = $code['id'];
			$codes[$key] = $code;
		}

		return $codes;
	}

	public function isFinal($kfu)
	{
		$code      = $kfu->getCode();
		$nextCodes = array($code . '.01', $code . '.02', $code . '.03');

		$count = $this->_em->createQuery('
			SELECT COUNT(c.ClPhPointerID)
			FROM VidalDrugBundle:ClinicoPhPointers c
			WHERE c.Code IN (:nextCodes)
		')->setParameter('nextCodes', $nextCodes)
			->getSingleScalarResult();

		return $count ? false : true;
	}

	public function findOneById($id)
	{
		return $this->_em->createQuery('
			SELECT c
			FROM VidalDrugBundle:ClinicoPhPointers c
			WHERE c.ClPhPointerID = :id
		')->setParameter('id', $id)
			->getOneOrNullResult();
	}

	public function findOneByCode($Code)
	{
		return $this->_em->createQuery('
			SELECT c
			FROM VidalDrugBundle:ClinicoPhPointers c
			WHERE c.Code = :Code
		')->setParameter('Code', $Code)
			->getOneOrNullResult();
	}

	public function findParent($kfu)
	{
		$code = $kfu->getCode();
		$pos  = strpos($code, '.');

		if ($pos === false) {
			return null;
		}

		$codes = explode('.', $code);
		array_pop($codes);
		$parentCode = implode('.', $codes);

		return $this->findOneByCode($parentCode);
	}

	public function findChildren($code)
	{
		$len = strlen($code) + 3;

		return $this->_em->createQuery('
			SELECT c
			FROM VidalDrugBundle:ClinicoPhPointers c
			WHERE c.Code LIKE :code
				AND LENGTH(c.Code) = :len
			ORDER BY c.Code ASC
		')->setParameters(array(
				'code' => $code . '%',
				'len'  => $len,
			))
			->getResult();
	}

	public function findBase($kfu)
	{
		$code = $kfu->getCode();
		$pos  = strpos($code, '.');

		if ($pos === false) {
			return null;
		}

		$codes = explode('.', $code);

		return $this->findOneByCode($codes[0]);
	}

	public function countProducts()
	{
		return $this->_em->createQuery('
			SELECT COUNT(p.ProductID) as countProducts, pointer.ClPhPointerID
			FROM VidalDrugBundle:Product p
			LEFT JOIN p.document d
			JOIN d.clphPointers pointer
			WHERE p.MarketStatusID IN (1,2,7)
				AND p.ProductTypeCode IN (\'DRUG\',\'GOME\')
				AND p.inactive = FALSE
			GROUP BY pointer.ClPhPointerID
		')->getResult();
	}
}