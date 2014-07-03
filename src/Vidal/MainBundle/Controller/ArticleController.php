<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Lsw\SecureControllerBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ArticleController extends Controller
{
	const ARTICLES_PER_PAGE = 14;
	const PHARM_PER_PAGE    = 5;

	/**
	 * Конкретная статья рубрики
	 * @Route("/encyclopedia/{rubrique}/{link}", name="article")
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

		$params = array(
			'title'     => $this->strip($article . '') . ' | ' . $rubrique,
			'menu_left' => 'articles',
			'rubrique'  => $rubrique,
			'article'   => $article,
			'documents' => $em->getRepository('VidalDrugBundle:Document')->findByArticle($article),
		);

		return $params;
	}

	/**
	 * Конкретная рубрика
	 * @Route("/encyclopedia/{rubrique}", name="rubrique")
	 *
	 * @Template("VidalMainBundle:Article:rubrique.html.twig")
	 */
	public function rubriqueAction($rubrique)
	{
		$em       = $this->getDoctrine()->getManager('drug');
		$rubrique = $em->getRepository('VidalDrugBundle:ArticleRubrique')->findEnabledByRubrique($rubrique);

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

	/** @Route("/poisk_preparatov/") */
	public function r2()
	{
		return $this->redirect($this->generateUrl('searche'), 301);
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
	 * @Route("/vracham/pharma-company/{id}", name="pharma_company")
	 * @Template("VidalMainBundle:Article:pharmaCompany.html.twig")
	 */
	public function pharmaCompanyAction(Request $request, $id)
	{
		if ($response = $this->checkRole()) {
			return $response;
		}

		$em      = $this->getDoctrine()->getManager('drug');
		$company = $em->getRepository('VidalDrugBundle:PharmCompany')->findOneById($id);

		if (!$company) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'title'     => $company . ' | Новости Фармацевтических компаний',
			'company'   => $company,
			'menu_left' => 'vracham',
		);

		$params['pagination'] = $this->get('knp_paginator')->paginate(
			$em->getRepository('VidalDrugBundle:PharmArticle')->getQueryOfCompany($id),
			$request->query->get('p', 1),
			self::PHARM_PER_PAGE
		);

		return $params;
	}

	/**
	 * @Route("/vracham/pharma-news", name="pharma_news")
	 * @Template("VidalMainBundle:Article:pharmaNews.html.twig")
	 */
	public function pharmaNewsAction(Request $request)
	{
		if ($response = $this->checkRole()) {
			return $response;
		}

		$em     = $this->getDoctrine()->getManager('drug');
		$params = array(
			'title'     => 'Новости Фармацевтических компаний',
			'menu_left' => 'vracham'
		);

		$params['pagination'] = $this->get('knp_paginator')->paginate(
			$em->getRepository('VidalDrugBundle:PharmArticle')->getQuery(),
			$request->query->get('p', 1),
			4
		);

		return $params;
	}

	/**
	 * @Route("/vracham", name="vracham")
	 * @Template("VidalMainBundle:Article:vracham.html.twig")
	 */
	public function vrachamAction()
	{
		if ($response = $this->checkRole()) {
			return $response;
		}

		$em = $this->getDoctrine()->getManager('drug');

		return array(
			'title'     => 'Информация для специалистов',
			'menu'      => 'vracham',
			'rubriques' => $em->getRepository('VidalDrugBundle:ArtRubrique')->findActive(),
			'arts'      => $em->getRepository('VidalDrugBundle:Art')->findForAnons(),
		);
	}

	/**
	 * @Route("/vracham/podrobno-o-preparate", name="portfolio")
	 *
	 * @Template("VidalMainBundle:Article:portfolio.html.twig")
	 */
	public function portfolioAction()
	{
		if ($response = $this->checkRole()) {
			return $response;
		}

		$em     = $this->getDoctrine()->getManager('drug');
		$params = array(
			'title'      => 'Портфели препаратов',
			'portfolios' => $em->getRepository('VidalDrugBundle:PharmPortfolio')->findActive(),
		);

		return $params;
	}

	/**
	 * @Route("/vracham/podrobno-o-preparate/{url}", name="portfolio_item")
	 * @Template("VidalMainBundle:Article:portfolioItem.html.twig")
	 */
	public function portfolioItemAction($url)
	{
		if ($response = $this->checkRole()) {
			return $response;
		}

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
	 * @Route("/vracham/expert/", name="vracham_expert")
	 * @Template
	 */
	public function vrachamExpertAction()
	{
		if ($response = $this->checkRole()) {
			return $response;
		}

		return array(
			'title' => 'Видаль-Эксперт',
			'menu'  => 'vracham',
		);
	}

	/**
	 * @Route("/vracham/expert/Vidal-CD/", name="vracham_expert_cd")
	 * @Template("VidalMainBundle:Article:vrachamExpertCd.html.twig")
	 */
	public function vrachamExpertCdAction()
	{
		if ($response = $this->checkRole()) {
			return $response;
		}

		return array(
			'menu'  => 'vracham',
			'title' => 'Электронный справочник Видаль',
		);
	}

	/**
	 * @Route("/vracham/Informatsiya-dlya-spetsialistov/{url}", requirements={"url"=".+"})
	 */
	public function r5($url)
	{
		//return $this->redirect($this->generateUrl('art', array('url' => $url)), 301);

		$parts        = explode('/', $url);
		$rubriqueName = $parts[0];
		$em           = $this->getDoctrine()->getManager('drug');
		$rubrique     = $em->getRepository('VidalDrugBundle:ArtRubrique')->findOneByUrl($rubriqueName);

		if (!$rubrique) {
			throw $this->createNotFoundException();
		}

		return $this->redirect($this->generateUrl('art', array('url' => $rubriqueName)), 301);
	}

	/**
	 * @Route("/vracham/{url}", name="art", requirements={"url"=".+"})
	 * @Template("VidalMainBundle:Article:art.html.twig")
	 */
	public function artAction(Request $request, $url)
	{
		if ($response = $this->checkRole()) {
			return $response;
		}

		$parts    = explode('/', $url);
		$em       = $this->getDoctrine()->getManager('drug');
		$rubrique = $em->getRepository('VidalDrugBundle:ArtRubrique')->findOneByUrl($parts[0]);
		$params   = array(
			'menu'     => 'vracham',
			'rubrique' => $rubrique,
		);

		# находим, если указана статья по старому
		$pos = strpos($url, '.');
		if ($pos !== false) {
			$index = count($parts) - 1;
			$link  = $parts[$index];
			$pos   = strpos($link, '.');
			$link  = substr($link, 0, $pos);
			$pos   = strpos($link, '_');
			if ($pos !== false) {
				$id                = substr($link, $pos + 1);
				$params['article'] = $em->getRepository('VidalDrugBundle:Art')->findOneById($id);
			}
			else {
				$params['article'] = $em->getRepository('VidalDrugBundle:Art')->findOneByLink($link);
			}
			array_pop($parts);
		}

		$pos = strpos($url, '~');
		if ($pos !== false) {
			$id                = substr($url, $pos + 1);
			$params['article'] = $em->getRepository('VidalDrugBundle:Art')->findOneById($id);
			array_pop($parts);
		}

		$count = count($parts);

		if ($count == 1) {
			$params['types']      = $em->getRepository('VidalDrugBundle:ArtType')->findByRubrique($rubrique);
			$params['pagination'] = $this->get('knp_paginator')->paginate(
				$em->getRepository('VidalDrugBundle:Art')->getQueryByRubrique($rubrique),
				$request->query->get('p', 1),
				self::ARTICLES_PER_PAGE
			);
		}
		elseif ($count == 2) {
			$type                 = $em->getRepository('VidalDrugBundle:ArtType')->rubriqueUrl($rubrique, $parts[1]);
			$params['type']       = $type;
			$params['categories'] = $em->getRepository('VidalDrugBundle:ArtCategory')->findByType($type);
			$params['pagination'] = $this->get('knp_paginator')->paginate(
				$em->getRepository('VidalDrugBundle:Art')->getQueryByType($type),
				$request->query->get('p', 1),
				self::ARTICLES_PER_PAGE
			);
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

		# формируем заголовок страницы
		$titles = array();
		if (isset($params['article'])) {
			$titles[] = $this->strip($params['article']->getTitle());
		}
		if (isset($params['category'])) {
			$titles[] = $params['category'];
		}
		if (isset($params['type'])) {
			$titles[] = $params['type'];
		}
		$titles[]        = $params['rubrique'];
		$params['title'] = implode(' | ', $titles);

		# отображение отдельной статьи своим шаблоном
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

	private function checkRole()
	{
		$response = null;
		$secutiry = $this->get('security.context');

		if (!$secutiry->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
			$response = $this->render('VidalMainBundle:Auth:login.html.twig', array(
				'title' => 'Закрытый раздел',
				'menu'  => 'vracham',
			));
		}
		elseif (!$secutiry->isGranted('ROLE_DOCTOR')) {
			$response = $this->render('VidalMainBundle:Auth:confirm.html.twig', array(
				'title' => 'Подтвердите e-mail',
				'menu'  => 'vracham',
				'user'  => $this->getUser(),
			));
		}

		return $response;
	}
}
