<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ArticleController extends Controller
{
	/**
	 * Конкретная статья рубрики
	 *
	 * @Route("/articles/{rubrique}/{link}", name="article")
	 * @Route("/patsientam/entsiklopediya/{rubrique}/{link}")
	 *
	 * @Template()
	 */
	public function articleAction($rubrique, $link)
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$rubrique = $em->getRepository('VidalDrugBundle:ArticleRubrique')->findOneByRubrique($rubrique);
		$article  = $em->getRepository('VidalDrugBundle:Article')->findOneByLink($link);

		if (!$rubrique || !$article) {
			throw $this->createNotFoundException();
		}

		return array(
			'title'     => $article . ' | ' . $rubrique,
			'menu_left' => 'articles',
			'rubrique'  => $rubrique,
			'article'   => $article
		);
	}

	/**
	 * Конкретная рубрика
	 *
	 * @Route("/articles/{rubrique}", name="rubrique")
	 * @Route("/patsientam/entsiklopediya/{rubrique}")
	 *
	 * @Template()
	 */
	public function rubriqueAction($rubrique)
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$rubrique = $em->getRepository('VidalDrugBundle:ArticleRubrique')->findOneByRubrique($rubrique);

		if (!$rubrique) {
			throw $this->createNotFoundException();
		}

		return array(
			'title'     => $rubrique . ' | Статьи',
			'menu_left' => 'articles',
			'rubrique'  => $rubrique,
			'articles'  => $em->getRepository('VidalDrugBundle:Article')->ofRubrique($rubrique)
		);
	}

	/**
	 * Рубрики статей видаля
	 *
	 * @Route("/articles", name="articles")
	 * @Route("/patsientam/entsiklopediya/")
	 *
	 * @Template()
	 */
	public function articlesAction()
	{
		$em = $this->getDoctrine()->getManager('drug');

		return array(
			'title'     => 'Статьи',
			'menu_left' => 'articles',
			'rubriques' => $em->getRepository('VidalDrugBundle:ArticleRubrique')->findActive()
		);
	}
}
