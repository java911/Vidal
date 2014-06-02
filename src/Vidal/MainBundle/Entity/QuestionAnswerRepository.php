<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class QuestionAnswerRepository extends EntityRepository
{
	public function findAll()
	{
		return $this->_em->createQueryBuilder()
			->select('qa')
			->from('VidalMainBundle:QuestionAnswer', 'qa')
			->where('qa.enabled = 1')
			->orderBy('qa.created', 'DESC')
			->getQuery()
			->getResult();
	}
}