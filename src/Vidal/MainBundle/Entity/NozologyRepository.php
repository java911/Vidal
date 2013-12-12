<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class NozologyRepository extends EntityRepository
{
	public function findByLetter($letter)
	{
		return $this->_em->createQuery('

		')->setParameter('letter', $letter)
			->getResult();
	}
}