<?php

namespace Vidal\DrugBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Vidal\DrugBundle\Entity\ArticleType;

class LoadUserData implements FixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$titles = array(
			'Описание симптома',
			'Описание заболевания',
			'Профилактика',
			'Лечение',
			'Диагностика',
			'Обзор препаратов',
			'Алгоритмы ведения пациентов',
			'Данные клинических исследований',
			'Особенности применения препарата',
			'Стандарты лечения',
		);

		foreach ($titles as $title) {
			$type = new ArticleType();
			$type->setTitle($title);
			$manager->persist($type);
		}

		$manager->flush();
	}
}