<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class DocumentRepository extends EntityRepository
{
	public function findById($id)
	{
		return $this->createQueryBuilder('d')
			->select('d')
			->where('d.DocumentID = :id')
			->setParameter('id', $id)
			->getQuery()
			->getOneOrNullResult();
	}

	public function findByName($name)
	{
		return $this->createQueryBuilder('d')
			->select('d')
			->where('d.EngName LIKE :name')
			->setParameter('name', $name.'%')
			->getQuery()
			->getOneOrNullResult();
	}
}