<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CityRepository extends EntityRepository
{
	public function getChoices()
	{
		$raw = $this->_em->createQuery('
			SELECT c.id, c.title, SIZE(c.doctors) as total, r.title as region, co.title as country
			FROM VidalMainBundle:City c
			JOIN c.region r
			JOIN c.country co
			WHERE c.doctors IS NOT EMPTY
			ORDER BY c.title ASC
		')->getResult();

		$cities = array();

		foreach ($raw as $r) {
			$key          = $r['id'];
			$cities[$key] = '[' . $r['total'] . '] ' . $r['title'] . ' -> ' . $r['region'] . ' -> ' . $r['country'];
		}

		return $cities;
	}

	public function getNames()
	{
		$raw = $this->_em->createQuery("
		 	SELECT c.title as city, r.title as region, co.title as country
		 	FROM VidalMainBundle:City c
		 	JOIN c.region r
		 	JOIN c.country co
		 	WHERE c.title != ''
		 		AND c.title IS NOT NULL
		 	ORDER BY c.title ASC
		")
			->setMaxResults(1000)
			->getResult();

		$names = array();

		foreach ($raw as $r) {
			$name  = trim($r['city']);
			$title = $name;

			if (!empty($r['region'])) {
				$title .= ', ' . trim($r['region']);
			}

			if (!empty($r['country'])) {
				$title .= ', ' . trim($r['country']);
			}

			$names[] = array('name' => $name, 'title' => $title);
		}

		return $names;
	}
}