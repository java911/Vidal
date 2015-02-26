<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DigestRepository extends EntityRepository
{
	public function get()
	{
		$digest = $this->_em->createQuery('
		 	SELECT d
		 	FROM VidalMainBundle:Digest d
		')->setMaxResults(1)
			->getOneOrNullResult();

		if (!$digest) {
			$digest = new Digest();
			$digest->setSubject('Тема письма');
			$digest->setText('<p>Текст письма</p>');
			$this->_em->persist($digest);
			$this->_em->flush($digest);
			$this->_em->refresh($digest);
		}

		return $digest;
	}
}