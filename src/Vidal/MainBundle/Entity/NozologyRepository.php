<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class NozologyRepository extends EntityRepository
{
	public function findByCode($code)
	{
		return $this->_em->createQuery('
			SELECT n.NozologyCode, n.Code, n.Name
			FROM VidalMainBundle:Nozology n
			WHERE n.Code = :code
		')->setParameter('code', $code)
			->getOneOrNullResult();
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
			$names[] = mb_strtolower($namesRaw[$i]['Name'], 'UTF-8');
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

	public function findByQuery($q)
	{
		$nozologies = $this->_em->createQuery('
			SELECT DISTINCT n.Code, n.Name
			FROM VidalMainBundle:Nozology n
			WHERE n.Name LIKE :q or n.Name LIKE :q2
			ORDER BY n.Name ASC
		')->setParameters(array(
				'q'  => $q . '%',
				'q2' => '% ' . $q . '%'
			))
			->getResult();

		for ($i=0, $c=count($nozologies); $i<$c; $i++) {
			$nozologies[$i]['Name'] = preg_replace('/' . $q . '/iu', '<span class="query">$0</span>', $nozologies[$i]['Name']);
		}

		return $nozologies;
	}

	public function findByDocumentId($DocumentID)
	{
		return $this->_em->createQuery('
			SELECT DISTINCT n.Code, n.Name
			FROM VidalMainBundle:Nozology n
			JOIN n.documents d WITH d = :DocumentID
			ORDER BY n.Name ASC
		')->setParameter('DocumentID', $DocumentID)
			->getResult();
	}
}