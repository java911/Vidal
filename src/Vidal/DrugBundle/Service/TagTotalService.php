<?php

namespace Vidal\DrugBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class TagTotalService
{
	private $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	public function count($tagId)
	{
		$em  = $this->container->get('doctrine')->getManager('drug');
		$tag = $em->getRepository('VidalDrugBundle:Tag')->findOneById($tagId);
		$pdo = $em->getConnection();

		if ($tag === null) {
			return false;
		}

		$total = 0;
		$total += $tag->getArticles()->count();
		$total += $tag->getArts()->count();
		$total += $tag->getPublications()->count();

		if ($infoPage = $tag->getInfoPage()) {
			$total += $infoPage->getArticles()->count();
			$total += $infoPage->getArts()->count();
			$total += $infoPage->getPublications()->count();
		}

		# приходится через PDO, так как в PostUpdate событии нельзя обновлять записи в базе данных
		$pdo->prepare("UPDATE tag SET total = $total WHERE id = $tagId")->execute();

		return true;
	}
}