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
	const PHARM_PER_PAGE    = 5;

	/**
	 * Конкретная статья рубрики
	 * @Route("/encyclopedia/{rubrique}/{link}", name="article")
	 *
	 * @Template("VidalMainBundle:Article:article.html.twig")
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
			'article'   => $article,
			'documents' => $em->getRepository('VidalDrugBundle:Document')->findByArticle($article),
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

	/** @Route("/patsientam/entsiklopediya/{url}", requirements={"url"=".+"}) */
	public function r3($url)
	{
		$em = $this->getDoctrine()->getManager('drug');

		if ($pos = strpos($url, '.')) {
			$url = substr($url, 0, $pos);
		}

		if ($pos = strpos($url, '_')) {
			$id      = substr($url, $pos + 1);
			$article = $em->getRepository('VidalDrugBundle:Article')->findOneById($id);
		}
		else {
			$parts = explode('/', $url);
			if (count($parts) > 1) {
				$lastIndex = count($parts) - 1;
				$link      = $parts[$lastIndex];
				$article   = $em->getRepository('VidalDrugBundle:Article')->findOneByLink($link);
			}
			else {
				$article = null;
			}
		}

		if (!$article) {
			throw $this->createNotFoundException();
		}

		return $this->redirect($this->generateUrl('article', array(
			'rubrique' => $article->getRubrique()->getRubrique(),
			'link'     => $article->getLink(),
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
	 * @Route("/vracham/pharma-news/{articleId}", name="pharma_news_item")
	 *
	 * @Template
	 */
	public function pharmaNewsItemAction($articleId)
	{
		$em      = $this->getDoctrine()->getManager('drug');
		$article = $em->getRepository('VidalDrugBundle:PharmArticle')->findOneById($articleId);

		if (!$article) {
			throw $this->createNotFoundException();
		}

		$company = $article->getCompany();

		$params = array(
			'title'     => $company . ' | Новости Фармацевтических компаний',
			'company'   => $company,
			'article'   => $article,
			'menu_left' => 'vracham'
		);

		return $params;
	}

	/**
	 * Новости фарм. компаний
	 *
	 * @Route("/vracham/pharma-news", name="pharma_news")
	 * @Secure(roles="ROLE_DOCTOR")
	 *
	 * @Template
	 */
	public function pharmaNewsAction(Request $request)
	{
		$em     = $this->getDoctrine()->getManager('drug');
		$params = array(
			'title'     => 'Новости Фармацевтических компаний',
			'menu_left' => 'vracham'
		);

		$params['pagination'] = $this->get('knp_paginator')->paginate(
			$em->getRepository('VidalDrugBundle:PharmArticle')->getQuery(),
			$request->query->get('p', 1),
			self::PHARM_PER_PAGE
		);

		return $params;
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
		$em        = $this->getDoctrine()->getManager('drug');
		$rubriques = $em->getRepository('VidalDrugBundle:ArtRubrique')->findActive();

		return array(
			'title'     => 'Информация для специалистов',
			'menu'      => 'vracham',
			'rubriques' => $rubriques,
		);
	}

	/**
	 * @Route("/vracham/podrobno-o-preparate", name="portfolio")
	 *
	 * @Template("VidalMainBundle:Article:portfolio.html.twig")
	 */
	public function portfolioAction()
	{
		$em     = $this->getDoctrine()->getManager('drug');
		$params = array(
			'title'      => 'Портфели препаратов',
			'portfolios' => $em->getRepository('VidalDrugBundle:PharmPortfolio')->findActive(),
		);

		return $params;
	}

	/**
	 * @Route("/vracham/podrobno-o-preparate/{url}", name="portfolio_item")
	 *
	 * @Template
	 */
	public function portfolioItemAction($url)
	{
		$em        = $this->getDoctrine()->getManager('drug');
		$portfolio = $em->getRepository('VidalDrugBundle:PharmPortfolio')->findOneByUrl($url);

		if (!$portfolio) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'title'     => $this->strip($portfolio->getTitle()) . ' | Портфель препарата',
			'portfolio' => $portfolio,
		);

		return $params;
	}

	/**
	 * @Secure(roles="ROLE_DOCTOR")
	 * @Route("/vracham/expert/Vidal-CD/", name="vracham_expert_cd")
	 * @Template()
	 */
	public function vrachamExpertCdAction()
	{
		return array();
	}

	/**
	 * Категории статей для врачей
	 *
	 * @Route("/vracham/{url}", name="art", requirements={"url"=".+"})
	 * @Secure(roles="ROLE_DOCTOR")
	 *
	 * @Template()
	 */
	public function artAction(Request $request, $url)
	{
		$parts    = explode('/', $url);
		$em       = $this->getDoctrine()->getManager('drug');
		$rubrique = $em->getRepository('VidalDrugBundle:ArtRubrique')->findOneByUrl($parts[0]);
		$params   = array(
			'menu'     => 'vracham',
			'rubrique' => $rubrique,
		);

		$pos = strpos($url, '~');
		if ($pos !== false) {
			$id                = substr($url, $pos + 1);
			$params['article'] = $em->getRepository('VidalDrugBundle:Art')->findOneById($id);
			array_pop($parts);
		}
		$count = count($parts);

		if ($count == 1) {
			if ($rubrique->getTypes()->count() == 0) {
				$params['pagination'] = $this->get('knp_paginator')->paginate(
					$em->getRepository('VidalDrugBundle:Art')->getQueryByRubrique($rubrique),
					$request->query->get('p', 1),
					self::ARTICLES_PER_PAGE
				);
			}
			else {
				$params['types'] = $em->getRepository('VidalDrugBundle:ArtType')->findByRubrique($rubrique);
			}
		}
		elseif ($count == 2) {
			$type           = $em->getRepository('VidalDrugBundle:ArtType')->rubriqueUrl($rubrique, $parts[1]);
			$params['type'] = $type;
			if ($params['type']->getCategories()->count() == 0) {
				$params['pagination'] = $this->get('knp_paginator')->paginate(
					$em->getRepository('VidalDrugBundle:Art')->getQueryByType($type),
					$request->query->get('p', 1),
					self::ARTICLES_PER_PAGE
				);
			}
			else {
				$params['categories'] = $em->getRepository('VidalDrugBundle:ArtCategory')->findByType($type);
			}
		}
		elseif ($count == 3) {
			$type                 = $em->getRepository('VidalDrugBundle:ArtType')->rubriqueUrl($rubrique, $parts[1]);
			$params['type']       = $type;
			$params['category']   = $em->getRepository('VidalDrugBundle:ArtCategory')->typeUrl($type, $parts[2]);
			$params['pagination'] = $this->get('knp_paginator')->paginate(
				$em->getRepository('VidalDrugBundle:Art')->getQueryByCategory($params['category']),
				$request->query->get('p', 1),
				self::ARTICLES_PER_PAGE
			);
		}

		if (isset($params['article'])) {
			return $this->render('VidalMainBundle:Article:art_item.html.twig', $params);
		}

		return $params;
	}

	private function strip($string)
	{
		$pat = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i', '/&amp;/', '/&reg;/');
		$rep = array('', '', '&', '');

		return preg_replace($pat, $rep, $string);
	}
}
