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
		 	LEFT JOIN c.region r
		 	LEFT JOIN c.country co
		 	WHERE c.title != ''
		 		AND c.title IS NOT NULL
		 	ORDER BY c.title ASC
		")->getResult();

		$names = array();

		foreach ($raw as $r) {
			$name = trim($r['city']);

			if (!empty($r['region'])) {
				$name .= ', ' . $r['region'];
			}

			if (!empty($r['country'])) {
				$name .= ', ' . $r['country'];
			}

			$names[] = $name;
		}

		$uniques = array();

		foreach ($names as $name) {
			if (!isset($uniques[$name])) {
				$uniques[$name] = '';
			}
		}

		return array_keys($uniques);
	}
}