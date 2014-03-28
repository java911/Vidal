<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MarketDrugRepository extends EntityRepository
{
    public function find($title)
    {
        return $this->_em->createQueryBuilder()
            ->select('md')
            ->from('VidalMainBundle:MarketDrug', 'md')
            ->where("md.title LIKE '$title%' ")
            ->orderBy('md.title', 'ASC')
            ->getQuery()->getResult();
    }

}