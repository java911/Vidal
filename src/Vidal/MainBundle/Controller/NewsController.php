<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class NewsController extends Controller
{
	const PUBLICATIONS_PER_PAGE = 22;
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

		$title = $this->strip($publication->getTitle());

		return array(
			'publication' => $publication,
			'menu_left'   => 'news',
			'title'       => $title . ' | Новости',
			'ogTitle'     => $title,
			'description' => $this->strip($publication->getAnnounce()),
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

	/** @Route("/share", name="share", options={"expose":true}) */
	public function shareAction(Request $request)
	{
		$my      = trim($request->request->get('my', ''));
		$friends = $request->request->get('friends', null);
		$text    = $request->request->get('text', null);

		if (!empty($my) && !empty($friends) && filter_var($request->request->get('my'), FILTER_VALIDATE_EMAIL)) {

			$emails = explode(';', $friends);
			$to     = array();

			# проверяем валидность адресов
			foreach ($emails as $email) {
				$email = trim($email);
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					return new JsonResponse('FAIL');
				}
				$to[] = $email;
			}

			$url   = $request->request->get('url', '');
			$title = urldecode($request->request->get('title', ''));

			# предотвратить отправку нескольких
			if ($this->get('prevent')->doubleClick()) {
				return new JsonResponse('DoubleClick');
			}

			# рассылаем
			$this->get('email.service')->send(
				$to,
				array('VidalMainBundle:Email:share.html.twig', array(
					'text'  => $text,
					'url'   => $url,
					'title' => $title,
				)),
				$my . ' поделился(-ась) с Вами: ' . $title,
				$my,
				false,
				$my
			);

			return new JsonResponse(implode('; ', $to));
		}

		return new JsonResponse('FAIL');
	}

	private function strip($string)
	{
		$string = strip_tags(html_entity_decode($string, ENT_QUOTES, 'UTF-8'));
		$string = preg_replace('/&nbsp;|®|™/', '', $string);

		return $string;
	}
}
