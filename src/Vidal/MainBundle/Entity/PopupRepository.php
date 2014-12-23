<?php

namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PopupRepository extends EntityRepository
{
    public function findPopup()
    {
        $bq = $this->createQueryBuilder('p')
            ->where('p.enabled = 1');
        $banners = $bq->getQuery()->getResult();

        $count   = count($banners);
        if ($count == 0) {
            return null;
        }

        if ($count == 1) {

            return $banners[0];
        }

        # логика для выборки случайного баннера по проценту
        $totalPresence = 0;
        $countRest     = 0;

        foreach ($banners as $banner) {
            $presence = $banner->getFrequency();
            $presence
                ? $totalPresence += $presence
                : $countRest += 1;
        }

        $presenceRest = $countRest ? (100 - $totalPresence) / $countRest : 0;
        $banner       = $this->getRandomBanner($banners, $presenceRest);


        return $banner;
    }

    private function getRandomBanner($banners, $presenceRest)
    {
        $count = count($banners);
        $rand  = mt_rand() / mt_getrandmax() * 100;
        $index = $count;

        for ($i = 0; $i < $count; $i++) {
            $banner   = $banners[$i];
            $presence = $banner->getFrequency();

            if (!$presence) {
                $presence = $presenceRest;
            }

            if ($rand > $presence) {
                $rand -= $presence;
            }
            else {
                $index = $i;
                break;
            }
        }

        return isset($banners[$index]) ? $banners[$index] : $banners[0];
    }
}