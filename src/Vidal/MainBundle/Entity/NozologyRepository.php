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

	public function findNozologyNames()
	{
		$names = array();

		$namesRaw = $this->_em->createQuery('
			SELECT n.Name
			FROM VidalMainBundle:Nozology n
			ORDER BY n.Name ASC
		')->getResult();

		for ($i = 0, $c = count($namesRaw); $i < $c; $i++) {
			$names[] = $namesRaw[$i]['Name'];
		}

		return $names;
	}

	public function findAll()
	{
		return $this->_em->createQuery('
		 	SELECT n.NozologyCode, n.Name
		 	FROM VidalMainBundle:Nozology n
		 	ORDER BY n.Name ASC
		')->getResult();
	}
}