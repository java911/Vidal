<?php

namespace Vidal\DrugBundle\Entity;

use Doctrine\ORM\EntityRepository;

class TagRepository extends EntityRepository
{
	public function createOrGet($text)
	{
		$tag = $this->_em->createQuery('
			SELECT t
			FROM VidalDrugBundle:Tag t
			WHERE t.text = :text
		')->setParameter('text', $text)
			->getOneOrNullResult();

		if (!$tag) {
			$tag = new Tag();
			$tag->setText($text);
			$this->_em->persist($tag);
			$this->_em->flush($tag);
		}

		return $tag;
	}
}