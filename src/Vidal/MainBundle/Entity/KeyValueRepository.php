<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class KeyValueRepository extends EntityRepository
{
	public function checkMatch($key, $value)
	{
		$password = $this->_em->createQuery('
		 	SELECT k
		 	FROM VidalMainBundle:KeyValue k
		 	WHERE k.key = :key
		')->setParameter('key', $key)
			->getOneOrNullResult();

		if (!$password) {
			return false;
		}

		return $value === $password->getValue();
	}
}