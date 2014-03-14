<?php

namespace Vidal\VeterinarBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ClinicoPhPointersRepository extends EntityRepository
{
	public function findForTree()
	{
		$results = $this->_em->createQuery('
		 	SELECT c.Code, c.Name, c.Level, c.ClPhPointerID, c.url, c.total
		 	FROM VidalVeterinarBundle:ClinicoPhPointers c
		 	ORDER BY c.Name
		')->getResult();

		$codes = array();

		foreach ($results as $code) {
			$key         = $code['Code'];
			$codes[$key] = $code;
		}

		return $codes;
	}

	public function isFinal($kfu)
	{
		$code      = $kfu->getCode();
		$nextCodes = array($code . '.01', $code . '.01', $code . '.03');

		$count = $this->_em->createQuery('
			SELECT COUNT(c.ClPhPointerID)
			FROM VidalVeterinarBundle:ClinicoPhPointers c
			WHERE c.Code IN (:nextCodes)
		')->setParameter('nextCodes', $nextCodes)
			->getSingleScalarResult();

		return $count ? false : true;
	}

	public function updateTotal($id, $total)
	{
		return $this->_em->createQuery('
			UPDATE VidalVeterinarBundle:ClinicoPhPointers c
			SET c.total = :total
			WHERE c.ClPhPointerID = :id
		')->setParameters(array(
				'id'    => $id,
				'total' => $total,
			))
			->execute();
	}
}