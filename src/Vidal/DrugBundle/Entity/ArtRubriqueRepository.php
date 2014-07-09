<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArtRubriqueRepository extends EntityRepository
{
	public function findActive()
	{
		return $this->_em->createQuery('
			SELECT r
			FROM VidalDrugBundle:ArtRubrique r
			WHERE r.enabled = TRUE
			ORDER BY r.priority DESC, r.title ASC
		')->getResult();
	}

	public function findSitemap()
	{
		$raw = $this->_em->createQuery('
			SELECT a.title, a.link,
				r.title rubriqueTitle, r.url rubriqueUrl,
				t.title typeTitle, t.url typeUrl,
				c.title categoryTitle, t.url categoryUrl
			FROM VidalDrugBundle:Art a
			JOIN a.rubrique r
			LEFT JOIN a.type t
			LEFT JOIN a.category c
			ORDER BY r.title, t.title, c.title, a.title
		')->getResult();

		# запихиваем в группы
		$result = array();

		foreach ($raw as $r) {
			$rubrique = $r['rubriqueUrl'];

			if (!$r['categoryUrl'] && !$r['typeUrl']) {
				# в рубрику
				if (isset($result[$rubrique])) {
					$result[$rubrique]['products'][] = $r;
				}
				else {
					$result[$rubrique]             = array();
					$result[$rubrique]['products'] = array($r);
					$result[$rubrique]['title']    = $r['rubriqueTitle'];
					$result[$rubrique]['url']      = $r['rubriqueUrl'];
				}
			}
			elseif (!$r['categoryUrl']) {
				$type = $r['typeUrl'];
				# в тип
				if (!isset($result[$rubrique])) {
					$result[$rubrique]             = array();
					$result[$rubrique]['children'] = array($type);
					$result[$rubrique]['title']    = $r['rubriqueTitle'];
					$result[$rubrique]['url']      = $r['rubriqueUrl'];
				}
			}
		}

		return $raw;
	}
}