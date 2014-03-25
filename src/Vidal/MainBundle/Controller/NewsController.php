<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NewsController extends Controller
{
	const PUBLICATIONS_PER_PAGE  = 25;
	const PUBLICATIONS_PER_PHARM = 5;

	/**
	 * @Route("/novosti/novosti_{id}.{ext}", name="publication_old", defaults={"ext"="html"})
	 * @Route("/novosti/{id}", name="publication")
	 * @Template()
	 */
	public function publicationAction($id)
	{
		$publication = $this->getDoctrine()->getRepository('VidalMainBundle:Publication')->findOneById($id);

		if (!$publication) {
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
	 * @Template()
	 */
	public function newsAction(Request $request)
	{
		$em     = $this->getDoctrine()->getManager();
		$params = array(
			'menu_left' => 'news',
			'title'     => 'Новости',
		);

		$params['publicationsPagination'] = $this->get('knp_paginator')->paginate(
			$em->getRepository('VidalMainBundle:Publication')->getQueryEnabled(),
			$request->query->get('p', 1),
			self::PUBLICATIONS_PER_PAGE
		);

		return $params;
	}

	/**
	 * @Route("Vidal/vidal-russia/Novosti-pharmatsevticheskih-kompanii", name="news_pharm")
	 */
//	public function newsPharmAction(Request $request)
//	{
//		$em     = $this->getDoctrine()->getManager();
//		$params = array(
//			'menu_left' => 'news',
//			'title'     => 'Новости фармацевтических компаний',
//		);
//
//		$params['publicationsPagination'] = $this->get('knp_paginator')->paginate(
//			$em->getRepository('VidalMainBundle:Publication')->getQueryPharm(),
//			$request->query->get('p', 1),
//			self::PUBLICATIONS_PER_PAGE
//		);
//
//		return $params;
//	}
}
