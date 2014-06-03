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

		if ($entity instanceof User) {
			if ($city = $entity->getCity()) {
				if ($region = $city->getRegion()) {
					$entity->setRegion($region);
				}
				if ($country = $city->getCountry()) {
					$entity->setCountry($country);
				}
			}
		}
	}

	public function preUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if ($entity instanceof User) {
			if ($city = $entity->getCity()) {
				if ($region = $city->getRegion()) {
					$entity->setRegion($region);
				}
				if ($country = $city->getCountry()) {
					$entity->setCountry($country);
				}
			}
		}
	}
}