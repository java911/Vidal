<?php

namespace Vidal\DrugBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Vidal\DrugBundle\Entity\Article;
use Vidal\DrugBundle\Entity\Art;
use Vidal\DrugBundle\Entity\Publication;

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
			'postPersist',
			'postUpdate',
		);
	}

	public function postPersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if ($entity instanceof Article || $entity instanceof Art || $entity instanceof Publication) {
			$this->setVideoMeta($entity);
		}
	}

	public function postUpdate(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if ($entity instanceof Article || $entity instanceof Art || $entity instanceof Publication) {
			$this->setVideoMeta($entity);
		}
	}

	private function setVideoMeta($entity)
	{
		$video = $entity->getVideo();

		if ($video && isset($video['path'])) {
			$rootDir = $this->container->get('kernel')->getRootDir() . '/../';
			require_once $rootDir . 'src/getID3/getid3.php';

			$getID3   = new \getID3;
			$filename = $rootDir . 'web' . $video['path'];
			$file     = $getID3->analyze($filename);

			$entity->setVideoWidth($file['video']['resolution_x']);
			$entity->setVideoHeight($file['video']['resolution_y']);
			$this->container->get('doctrine')->getManager('drug')->flush($entity);
		}
	}
}