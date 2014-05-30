<?php

namespace Vidal\MainBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Vidal\MainBundle\Entity\User;

class DoctrineEventSubscriber implements EventSubscriber
{
	private $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/**
	 * Возвращает список имён событий, которые обрабатывает данный класс. Callback-методы должны иметь такие же имена
	 */
	public function getSubscribedEvents()
	{
		return array(
			'prePersist',
			'preUpdate',
		);
	}

	public function prePersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		# пользователю надо прописать регион
		if ($entity instanceof User && !$entity->getOldUser()) {
			if ($region = $entity->getCity()->getRegion()) {
				$entity->setRegion($region);
			}
			if ($country = $entity->getCity()->getCountry()) {
				$entity->setCountry($country);
			}
		}
	}

	public function preUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		# пользователю надо прописать регион
		if ($entity instanceof User && !$entity->getOldUser()) {
			if ($region = $entity->getCity()->getRegion()) {
				$entity->setRegion($region);
			}
			if ($country = $entity->getCity()->getCountry()) {
				$entity->setCountry($country);
			}
		}
	}
}