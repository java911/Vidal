<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
	public function findOneByLogin($login)
	{
		return $this->_em->createQuery('
			SELECT u
			FROM VidalMainBundle:User u
			WHERE u.username = :login
				OR u.oldLogin = :login
		')->setParameter('login', $login)
			->getOneOrNullResult();
	}
}