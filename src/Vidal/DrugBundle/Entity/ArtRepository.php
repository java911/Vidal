<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArtRepository extends EntityRepository
{
	public function getQueryByRubrique($rubrique)
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.rubrique = :id
				AND a.type IS NULL
				AND a.enabled = TRUE
				AND a.date < :now
			ORDER BY a.date DESC, a.priority DESC
		')->setParameter('now', new \DateTime())
			->setParameter('id', $rubrique->getId());
	}

	public function getQueryByType($type)
	{
		if (!$type) {
			return null;
		}

		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.type = :id
				AND a.category IS NULL
				AND a.enabled = TRUE
				AND a.date < :now
			ORDER BY a.date DESC, a.priority DESC
		')->setParameter('now', new \DateTime())
			->setParameter('id', $type->getId());
	}

	public function getQueryByCategory($category)
	{
		if (!$category) {
			return null;
		}

		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.category = :id
				AND a.enabled = TRUE
				AND a.date < :now
			ORDER BY a.date DESC, a.priority DESC
		')->setParameter('now', new \DateTime())
			->setParameter('id', $category->getId());
	}

	public function atIndex()
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalDrugBundle:Art a
		 	WHERE a.atIndex = TRUE
		 		AND a.date < :now
		 	ORDER BY a.date DESC
		')->setParameter('now', new \DateTime())
			->setMaxResults(1)
			->getOneOrNullResult();
	}

	public function findForAnons()
	{
		return $this->_em->createQuery('
			SELECT a
			FROM VidalDrugBundle:Art a
			WHERE a.anons = TRUE
				AND a.enabled = TRUE
				AND a.date < :now
			ORDER BY a.anonsPriority DESC, a.date DESC
		')->setParameter('now', new \DateTime())
			->getResult();
	}

	public function getQueryByTag($tagId)
	{
		return $this->_em->createQuery('
		 	SELECT a
		 	FROM VidalDrugBundle:Art a
		 	JOIN a.tags t
		 	WHERE a.date < :now
		 		AND t = :tagId
		 	ORDER BY a.date DESC
		')->setParameter('now', new \DateTime())
			->setParameter('tagId', $tagId)
			->getResult();
	}

	public function findByTagWord($tagId, $text)
	{
		if (empty($text)) {
			$results1 = $this->_em->createQuery('
				SELECT a
				FROM VidalDrugBundle:Art a
				JOIN a.tags t WITH t = :tagId
			')->setParameter('tagId', $tagId)
				->getResult();

			$results2 = $this->_em->createQuery('
				SELECT a
				FROM VidalDrugBundle:Art a
				JOIN a.infoPages i
				JOIN i.tag t WITH t = :tagId
			')->setParameter('tagId', $tagId)
				->getResult();

			return array_merge($results1, $results2);
		}
		else {
			$tagHistory = $this->_em->getRepository('VidalDrugBundle:TagHistory')->findOneByTagText($tagId, $text);
			$ids        = $tagHistory->getArtIds();

			if (empty($ids)) {
				return array();
			}

			return $this->_em->createQuery('
				SELECT a
				FROM VidalDrugBundle:Art a
				WHERE a.id IN (:ids)
			')->setParameter('ids', $ids)
				->getResult();
		}
	}
}