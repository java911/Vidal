<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TagController extends Controller
{
	const NEWS_PER_PAGE = 12;
	const PHARM_PER_PAGE = 4;

	/**
	 * @Route("/tag/news/{id}", name="tag_news")
	 * @Template("VidalMainBundle:Tag:tag_news.html.twig")
	 */
	public function tagNewsAction(Request $request, $id)
	{
		$em   = $this->getDoctrine()->getManager('drug');
		$tag  = $em->getRepository('VidalDrugBundle:Tag')->findOneById($id);
		$page = $request->query->get('p', 1);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'tag'   => $tag,
			'title' => $tag->getText() . ' | Новости',
		);

		$query                = $em->getRepository('VidalDrugBundle:Publication')->getQueryByTag($id);
		$params['pagination'] = $this->get('knp_paginator')->paginate($query, $page, self::NEWS_PER_PAGE);

		return $params;
	}

	/**
	 * @Route("/tag/articles/{id}", name="tag_articles")
	 * @Template("VidalMainBundle:Tag:tag_articles.html.twig")
	 */
	public function tagArticlesAction(Request $request, $id)
	{
		$em   = $this->getDoctrine()->getManager('drug');
		$tag  = $em->getRepository('VidalDrugBundle:Tag')->findOneById($id);
		$page = $request->query->get('p', 1);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'tag'   => $tag,
			'title' => $tag->getText() . ' | Медицинская энциклопедия',
		);

		$query                = $em->getRepository('VidalDrugBundle:Article')->getQueryByTag($id);
		$params['pagination'] = $this->get('knp_paginator')->paginate($query, $page, self::NEWS_PER_PAGE);

		return $params;
	}

	/**
	 * @Route("/tag/arts/{id}", name="tag_arts")
	 * @Template("VidalMainBundle:Tag:tag_arts.html.twig")
	 */
	public function tagArtsAction(Request $request, $id)
	{
		$em   = $this->getDoctrine()->getManager('drug');
		$tag  = $em->getRepository('VidalDrugBundle:Tag')->findOneById($id);
		$page = $request->query->get('p', 1);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'tag'   => $tag,
			'title' => $tag->getText() . ' | Статьи специалистам',
		);

		$query                = $em->getRepository('VidalDrugBundle:Art')->getQueryByTag($id);
		$params['pagination'] = $this->get('knp_paginator')->paginate($query, $page, self::NEWS_PER_PAGE);

		return $params;
	}

	/**
	 * @Route("/tag/pharm-articles/{id}", name="tag_pharm_articles")
	 * @Template("VidalMainBundle:Tag:tag_pharm_articles.html.twig")
	 */
	public function tagPharmArticlesAction(Request $request, $id)
	{
		$em   = $this->getDoctrine()->getManager('drug');
		$tag  = $em->getRepository('VidalDrugBundle:Tag')->findOneById($id);
		$page = $request->query->get('p', 1);

		if (!$tag) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'tag'   => $tag,
			'title' => $tag->getText() . ' | Статьи специалистам',
		);

		$query                = $em->getRepository('VidalDrugBundle:PharmArticle')->getQueryByTag($id);
		$params['pagination'] = $this->get('knp_paginator')->paginate($query, $page, self::PHARM_PER_PAGE);

		return $params;
	}
}
