<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Lsw\SecureControllerBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArticleController extends Controller
{
	const ARTICLES_PER_PAGE = 14;

	/**
	 * Конкретная статья рубрики
	 * @Route("/encyclopedia/{rubrique}/{link}", name="article")
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
			'title'     => $this->strip($article . '') . ' | ' . $rubrique,
			'menu_left' => 'articles',
			'rubrique'  => $rubrique,
			'article'   => $article
		);
	}

	/**
	 * Конкретная рубрика
	 * @Route("/encyclopedia/{rubrique}", name="rubrique")
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

		$articles = $em->getRepository('VidalDrugBundle:Article')->ofRubrique($rubrique);

		return array(
			'title'     => $rubrique . ' | Энциклопедия',
			'menu_left' => 'articles',
			'rubrique'  => $rubrique,
			'articles'  => $articles,
		);
	}

	/**
	 * @Route("/patsientam/entsiklopediya/")
	 * @Route("/patsientam/entsiklopediya")
	 */
	public function r1()
	{
		return $this->redirect($this->generateUrl('articles'), 301);
	}

	/**
	 * @Route("/patsientam/entsiklopediya/{rubrique}/")
	 */
	public function r2($rubrique)
	{
		return $this->redirect($this->generateUrl('rubrique', array('rubrique' => $rubrique), 301));
	}

	/** @Route("/patsientam/entsiklopediya/{rubrique}/{link}.{ext}", defaults={"ext":"html"}) */
	public function r3($rubrique, $link)
	{
		return $this->redirect($this->generateUrl('article', array(
			'rubrique' => $rubrique,
			'link'     => $link,
		), 301));
	}

	/**
	 * @Route("/vracham/Informatsiya-dlya-spetsialistov")
	 * @Route("/vracham/Informatsiya-dlya-spetsialistov/")
	 */
	public function r4()
	{
		return $this->redirect($this->generateUrl('vracham'), 301);
	}

	/**
	 * Рубрики статей видаля
	 * @Route("/encyclopedia", name="articles")
	 *
	 * @Template()
	 */
	public function articlesAction()
	{
		$em = $this->getDoctrine()->getManager('drug');

		return array(
			'title'     => 'Энциклопедия',
			'menu_left' => 'articles',
			'rubriques' => $em->getRepository('VidalDrugBundle:ArticleRubrique')->findEnabled()
		);
	}

	/**
	 * Категории статей для врачей
	 *
	 * @Route("/vracham", name="vracham")
	 * @Secure(roles="ROLE_DOCTOR")
	 *
	 * @Template()
	 */
	public function vrachamAction()
	{
		$em   = $this->getDoctrine()->getManager('drug');
		$subs = $em->getRepository('VidalDrugBundle:Subdivision')->findVracham();

		return array(
			'title' => 'Информация для специалистов',
			'menu'  => 'vracham',
			'subs'  => $subs,
		);
	}

	/**
	 * Отдельная категория (subdivision) со статьями
	 *
	 * @Route("/vracham/Informatsiya-dlya-spetsialistov/{url}", name="vracham_url", requirements={"url"=".+"})
	 * @Route("/vracham/Informatsiya-dlya-spetsialistov/{url}/", requirements={"url"=".+"})
	 * @Secure(roles="ROLE_DOCTOR")
	 *
	 * @Template()
	 */
	public function vrachamUrlAction(Request $request, $url)
	{
		$em      = $this->getDoctrine()->getManager('drug');
		$urlFull = $request->getPathInfo();
		$params  = array(
			'title' => 'Информация для специалистов',
			'menu'  => 'vracham',
		);

		$urlParts = explode('/', $url);
		$paths    = array();

		foreach ($urlParts as $part) {
			$pos  = strrpos($part, '_');
			$pos2 = false;

			if ($pos === false) {
				$pos2 = strpos($part, '.html');
			}

			# если это путь - заносим в массив для хлебных крошек, иначе - это статья
			if ($pos === false && $pos2 === false) {
				if ($part != '') {
					$path = $em->getRepository('VidalDrugBundle:Subdivision')->findOneByEngName($part);
					if ($path) {
						$paths[] = $path;
					}
				}
			}
			else {
				if ($pos) {
					# надо отсечь с конца .html
					$posDot = strpos($part, '.');
					if ($posDot !== false) {
						$part = substr($part, 0, $posDot);
					}
					$id      = substr($part, $pos + 1);
					$article = $em->getRepository('VidalDrugBundle:Art')->findOneById($id);
				}
				else {
					$link    = substr($part, 0, $pos2);
					$article = $em->getRepository('VidalDrugBundle:Art')->findOneByLink($link);
				}

				if (!$article) {
					throw $this->createNotFoundException();
				}

				$index             = count($paths) - 1;
				$params['sub']     = $paths[$index];
				$params['article'] = $article;
				$params['paths']   = $paths;

				return $this->render('VidalMainBundle:Article:vrachamUrlArticle.html.twig', $params);
			}
		}

		if (substr($urlFull, -1) != '/') {
			$urlFull .= '/';
		}

		$sub = $em->getRepository('VidalDrugBundle:Subdivision')->findOneByUrl($urlFull);

		if (empty($sub)) {
			throw $this->createNotFoundException();
		}

		$params['sub']   = $sub;
		$params['paths'] = $paths;

		$params['pagination'] = $this->get('knp_paginator')->paginate(
			$em->getRepository('VidalDrugBundle:Art')->getQueryBySubdivision($sub->getId()),
			$request->query->get('p', 1),
			self::ARTICLES_PER_PAGE
		);

		return $params;
	}

	private function strip($string)
	{
		$pat = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i', '/&amp;/');
		$rep = array('', '', '&');

		return preg_replace($pat, $rep, $string);
	}
}
