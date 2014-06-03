<?php
namespace Vidal\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BannerRepository extends EntityRepository
{
	public function countClick($bannerId)
	{
            $this->_em->createQuery('
                UPDATE VidalMainBundle:Banner b
                SET
                  b.clicks = b.clicks + 1
                WHERE b = :bannerId
            ')->setParameter('bannerId', $bannerId)
                ->execute();

	}

	public function findByGroup($groupId)
	{
		$qb = $this->createQueryBuilder('b');
		$qb->select('b')
			->leftJoin('b.group', 'g')
			->andWhere('g = :groupId')
			->andWhere('g.enabled = TRUE')
			->andWhere('b.enabled = TRUE')
			->andWhere('b.starts < CURRENT_TIMESTAMP()')
			->andWhere('b.ends IS NULL OR b.ends > CURRENT_TIMESTAMP()')
			->andWhere('b.expires IS NULL OR b.expires > 0')
			->setParameter('groupId', $groupId);

		$banners = $qb->getQuery()->getResult();

        # Удаляем варианты где дневной лимит >= показам за сегодня
        $banners2= array();
        foreach ($banners as $key => $val){

            # Если это вчерашняя статистика по дням, то обнуляем ее и меняем сегодняшную дату
            $tmpDate = new \DateTime('now');
            if ($val->getDateDay() < $tmpDate){
                $this->newDate($tmpDate, $val->getId());
            }
            if ( $val->getLimitDay() != 0 && $val->getLimitDay() <= $val->getClickDay() ){
                unset($banners[$key]);
            }else{
                $banners2[] = $val;
            }
        }

        $banners = $banners2;

        $count   = count($banners);
		if ($count == 0) {
			return null;
		}

		if ($count == 1) {
			$this->countShow($banners[0]);

			return $banners[0];
		}

		# логика для выборки случайного баннера по проценту
		$totalPresence = 0;
		$countRest     = 0;

		foreach ($banners as $banner) {
			$presence = $banner->getPresence();
			$presence
				? $totalPresence += $presence
				: $countRest += 1;
		}

		$presenceRest = $countRest ? (100 - $totalPresence) / $countRest : 0;
		$banner       = $this->getRandomBanner($banners, $presenceRest);

		if ($reference = $banner->getReference()) {
			$banner = $reference;
		}

		$this->countShow($banner);

		return $banner;
	}

    public function newDate($date, $bannerId){
        $date = $date->format('Y-m-d').' 23:59:59';
        $this->_em->createQuery("
                UPDATE VidalMainBundle:Banner b
                SET  b.clickDay = 0, b.dateDay = '$date'
                WHERE b = :bannerId
            ")->setParameter('bannerId', $bannerId)
            ->execute();
    }

	private function getRandomBanner($banners, $presenceRest)
	{
		$count = count($banners);
		$rand  = mt_rand() / mt_getrandmax() * 100;
		$index = $count;

		for ($i = 0; $i < $count; $i++) {
			$banner   = $banners[$i];
			$presence = $banner->getPresence();

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

		return $banners[$index];
	}

	private function countShow($banner)
	{
		if ($reference = $banner->getReference()) {
			$banner = $reference;
		}

		if ($banner->getExpires()) {
			$this->_em->createQuery('
				UPDATE VidalMainBundle:Banner b
				SET b.displayed = b.displayed + 1,
					b.expires = b.expires - 1,
					b.clickDay = b.clickDay + 1
				WHERE b = :bannerId
			')->setParameter('bannerId', $banner->getId())
				->execute();
		}
		else {
			$this->_em->createQuery('
				UPDATE VidalMainBundle:Banner b
				SET b.displayed = b.displayed + 1
				WHERE b = :bannerId
			')->setParameter('bannerId', $banner->getId())
				->execute();
		}
	}
}