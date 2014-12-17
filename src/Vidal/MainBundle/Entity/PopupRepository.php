<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PopupRepository extends EntityRepository
{
    public function findPopup()
    {
        $bq = $this->createQueryBuilder('p')
            ->where('p.enabled = 1');
        return shuffle($bq->getQuery()->getResult());
    }
}