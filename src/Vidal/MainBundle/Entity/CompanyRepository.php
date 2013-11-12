<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompanyRepository extends EntityRepository
{
	public function findByDocumentID($id)
	{
		return $this->_em->createQuery('
			SELECT c
			FROM VidalMainBundle:Document d
			LEFT JOIN VidalMainBundle:ProductDocument pd WITH pd.DocumentID = d
			LEFT JOIN VidalMainBundle:Product p WITH pd.ProductID = p
			LEFT JOIN VidalMainBundle:ProductCompany pc WITH pc.ProductID = p
			LEFT JOIN VidalMainBundle:Company c WITH pc.CompanyID = c
			WHERE d = :id AND pc.ItsMainCompany = 1
			ORDER BY pd.Ranking DESC
		')->setParameter('id', $id)
			->setMaxResults(1)
			->getOneOrNullResult();
	}
}