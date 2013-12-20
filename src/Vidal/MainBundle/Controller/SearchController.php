<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class SearchController extends Controller
{
	const PRODUCTS_PER_PAGE = 30;

	/**
	 * @Route("/search", name="search")
	 *
	 * @Template("VidalMainBundle:Search:search.html.twig")
	 */
	public function searchAction(Request $request)
	{
		$em     = $this->getDoctrine()->getManager();
		$q      = $request->query->get('q', ''); # поисковый запрос
		$q      = trim($q);
		$t      = $request->query->get('t', 'all'); # тип запроса из селект-бокса
		$p      = $request->query->get('p', 1); # номер страницы
		$params = array('q' => $q, 't' => $t);

		# поисковый запрос не может быть меньше 2
		if (mb_strlen($q, 'UTF-8') < 2) {
			return $this->render('VidalMainBundle:Search:search_too_short.html.twig', $params);
		}

		# для некоторых типов запроса надо найти основание слова (чтоб не учитывать окончание)
		if (in_array($t, array('all', 'molecule', 'atc'))) {
			$q = $this->get('lingua.service')->stem_string($q);
		}

		if ($t == 'all' || $t == 'product') {
			$products = $em->getRepository('VidalMainBundle:Product')->findByQuery($q);

			$paginator                    = $this->get('knp_paginator');
			$pagination                   = $paginator->paginate($products, $p, self::PRODUCTS_PER_PAGE);
			$params['productsPagination'] = $pagination;

			if ($pagination->getTotalItemCount()) {
				$productIds          = $this->getProductIds($pagination);
				$params['companies'] = $em->getRepository('VidalMainBundle:Company')->findByProducts($productIds);
				$params['pictures']  = $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds);
			}
		}

		# поиск по активному веществу
		if (($t == 'all' || $t == 'molecule') && $p == 1) {
			$params['molecules'] = $em->getRepository('VidalMainBundle:Molecule')->findByQuery($q);
		}

		# поиск по АТХ коду
		if ($t == 'atc') {
			$params['atcCodes'] = $em->getRepository('VidalMainBundle:ATC')->findByQuery($q);
		}

		# поиск по производителю
		if ($t == 'firm') {
			$params['firms'] = $em->getRepository('VidalMainBundle:Company')->findByQuery($q);
		}

		return $params;
	}

	/**
	 * @Route("/searche", name="searche")
	 *
	 * @Template("VidalMainBundle:Search:searche.html.twig")
	 */
	public function searcheAction(Request $request)
	{
		$em          = $this->getDoctrine()->getManager();
		$q           = $request->query->get('q', ''); # поисковый запрос
		$q           = trim($q);
		$t           = $request->query->get('t', 'all'); # тип запроса из селект-бокса
		$p           = $request->query->get('p', 1); # номер страницы
		$badIncluded = $request->query->has('b'); # включать ли бады
		$params      = array('q' => $q, 't' => $t);

		# поисковый запрос не может быть меньше 2
		if (empty($q)) {
			return $params;
		}
		elseif (mb_strlen($q, 'UTF-8') < 2) {
			return $this->render('VidalMainBundle:Search:searche_too_short.html.twig', $params);
		}

		# для некоторых типов запроса надо найти основание слова (чтоб не учитывать окончание)
		if (in_array($t, array('all', 'molecule', 'atc', 'nosology', 'clphgroup', 'phthgroup'))) {
			$q = $this->get('lingua.service')->stem_string($q);
		}

		if ($t == 'all' || $t == 'product') {
			$products                     = $em->getRepository('VidalMainBundle:Product')->findByQuery($q, $badIncluded);
			$paginator                    = $this->get('knp_paginator');
			$pagination                   = $paginator->paginate($products, $p, self::PRODUCTS_PER_PAGE);
			$params['productsPagination'] = $pagination;

			if ($pagination->getTotalItemCount()) {
				$productIds          = $this->getProductIds($pagination);
				$params['companies'] = $em->getRepository('VidalMainBundle:Company')->findByProducts($productIds);;
				$params['pictures'] = $em->getRepository('VidalMainBundle:Picture')->findByProductIds($productIds);
			}
		}

		# на следующих страницах отображаются только препараты
		if ($p == 1) {
			# поиск по активному веществу
			if ($t == 'all' || $t == 'molecule') {
				$params['molecules'] = $em->getRepository('VidalMainBundle:Molecule')->findByQuery($q);
			}

			# поиск по показаниям (МКБ-10) - Nozology
			if ($t == 'all' || $t == 'nosology') {
				$params['nozologies'] = $em->getRepository('VidalMainBundle:Nozology')->findByQuery($q);
			}

			# поиск по АТХ коду
			if ($t == 'atc') {
				$params['atcCodes'] = $em->getRepository('VidalMainBundle:ATC')->findByQuery($q);
				$params['atcTree']  = true;
			}

			# поиск по производителю
			if ($t == 'firm') {
				$params['firms'] = $em->getRepository('VidalMainBundle:Company')->findByQuery($q);
			}

			# поиск по клиннико-фармакологической группе
			if ($t == 'clphgroup') {
				$params['clphgroups'] = $em->getRepository('VidalMainBundle:Document')->findClPhGroupsByQuery($q);
			}

			# поиск по фармако-терапевтической группе
			if ($t == 'phthgroup') {
				$params['phthgroups'] = $em->getRepository('VidalMainBundle:Product')->findPhThGroupsByQuery($q);
			}
		}

		return $params;
	}

	/** Получить массив идентификаторов продуктов */
	private function getProductIds($products)
	{
		$productIds = array();

		foreach ($products as $product) {
			$productIds[] = $product['ProductID'];
		}

		return $productIds;
	}

	/**
	 * @Cache(expires="tomorrow", public="true")
	 */
	public function treeAtcAction()
	{
		$em         = $this->getDoctrine()->getManager();
		$atcCodes   = $em->getRepository('VidalMainBundle:ATC')->findAll();
		$atcGrouped = array();

		# надо сгруппировать по родителю
		for ($i = 8; $i > 1; $i--) {
			foreach ($atcCodes as $code => $atc) {
				if (strlen($code) == $i && isset($atc['ParentATCCode'])) {
					$key                           = $atc['ParentATCCode'];
					$code                          = $atc['ATCCode'];
					$atcCodes[$key]['list'][$code] = $atc;
				}
			}
		}

		# взять только первый уровень [A, B, C]
		foreach ($atcCodes as $code => $atc) {
			if (strlen($code) == 1) {
				$atcGrouped[$code] = $atc;
			}
		}

		return $this->render('VidalMainBundle:Search:tree_atc.html.twig', array(
			'atcGrouped' => $atcGrouped,
		));
	}
}
