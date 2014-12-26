<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends Controller
{
	const PUBLICATIONS_PER_PAGE  = 22;
	const PUBLICATIONS_PER_PHARM = 5;

	/** @Route("/novosti/novosti_{id}.{ext}", defaults={"ext"="html"}) */
	public function r1($id)
	{
		return $this->redirect($this->generateUrl('publication', array('id' => $id)), 301);
	}

	/**
	 * @Route("/novosti/{id}", name="publication")
	 * @Template("VidalMainBundle:News:publication.html.twig")
	 */
	public function publicationAction(Request $request, $id)
	{
		$em          = $this->getDoctrine()->getManager('drug');
		$publication = $em->getRepository('VidalDrugBundle:Publication')->findOneById($id);

		if ((!$publication || $publication->getEnabled() === false) && !$request->query->has('test')) {
			throw $this->createNotFoundException();
		}

		return array(
			'publication' => $publication,
			'menu_left'   => 'news',
			'title'       => $this->strip($publication->getTitle()) . ' | Новости',
		);
	}

	/**
	 * @Route("/novosti", name="news")
	 * @Template("VidalMainBundle:News:news.html.twig")
	 */
	public function newsAction(Request $request)
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$page     = $request->query->get('p', 1);
		$testMode = $request->query->has('test');

		$params = array(
			'menu_left' => 'news',
			'title'     => 'Новости',
		);

		if ($page == 1) {
			$params['publicationsPriority'] = $em->getRepository('VidalDrugBundle:Publication')->findLastPriority($testMode);
		}

		$params['publicationsPagination'] = $this->get('knp_paginator')->paginate(
			$em->getRepository('VidalDrugBundle:Publication')->getQueryEnabled($testMode),
			$page,
			self::PUBLICATIONS_PER_PAGE
		);

		return $params;
	}

	/** @Route("/novost-test", name="novost-test") */
	public function novostTestAction()
	{
		return $this->redirect($this->generateUrl('publication', array('id' => 4618, 'test' => '')));
	}

	private function strip($string)
	{
		$string = preg_replace('/&(.+);/', '', $string);

		return strip_tags($string);
	}
}
