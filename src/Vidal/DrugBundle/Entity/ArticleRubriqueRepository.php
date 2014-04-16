<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ArticleRubriqueRepository extends EntityRepository
{
	public function getByTitle($title, $category)
	{
		$rubrique = $this->_em->createQuery('
			SELECT r
			FROM VidalDrugBundle:ArticleRubrique r
			WHERE r.title = :title
		')->setParameter('title', $title)
			->getOneOrNullResult();

		if (empty($rubrique)) {
			$rubrique = new ArticleRubrique();
			$rubrique->setTitle($title);

			if (!empty($category)) {
				$rubrique->setRubrique($category);
			}

			$this->_em->persist($rubrique);
			$this->_em->flush($rubrique);
			$this->_em->refresh($rubrique);
		}

		return $rubrique;
	}

	public function findEnabled()
	{
		return $this->_em->createQuery('
			SELECT r
			FROM VidalDrugBundle:ArticleRubrique r
			WHERE r.enabled = TRUE
			ORDER BY r.priority DESC, r.title ASC
		')->getResult();
	}
}