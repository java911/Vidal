<?php

namespace Vidal\VeterinarBundle\Controller;

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
	 * @Route("/veterinar/search", name="v_search")
	 *
	 * @Template("VidalVeterinarBundle:Search:search.html.twig")
	 */
	public function searchAction(Request $request)
	{
		$em     = $this->getDoctrine()->getManager('veterinar');
		$q      = $request->query->get('q', ''); # поисковый запрос
		$q      = trim($q);
		$t      = $request->query->get('t', 'all'); # тип запроса из селект-бокса
		$p      = $request->query->get('p', 1); # номер страницы
		$params = array('q' => $q, 't' => $t);

		# поисковый запрос не может быть меньше 2
		if (mb_strlen($q, 'UTF-8') < 2) {
			return $this->render('VidalVeterinarBundle:Search:search_too_short.html.twig', $params);
		}

		# для некоторых типов запроса надо найти основание слова (чтоб не учитывать окончание)
		if (in_array($t, array('all', 'molecule', 'atc'))) {
			$q = $this->get('lingua.service')->stem_string($q);
		}

		if ($t == 'all' || $t == 'product') {
			$products = $em->getRepository('VidalVeterinarBundle:Product')->findByQuery($q);

			$paginator                    = $this->get('knp_paginator');
			$pagination                   = $paginator->paginate($products, $p, self::PRODUCTS_PER_PAGE);
			$params['productsPagination'] = $pagination;

			if ($pagination->getTotalItemCount()) {
				$productIds          = $this->getProductIds($pagination);
				$params['companies'] = $em->getRepository('VidalVeterinarBundle:Company')->findByProducts($productIds);
				$params['pictures']  = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
			}
		}

		# поиск по активному веществу
		if (($t == 'all' || $t == 'molecule') && $p == 1) {
			$params['molecules'] = $em->getRepository('VidalVeterinarBundle:Molecule')->findByQuery($q);
		}

		# поиск по АТХ коду
		if ($t == 'atc') {
			$params['atcCodes'] = $em->getRepository('VidalVeterinarBundle:ATC')->findByQuery($q);
		}

		# поиск по производителю
		if ($t == 'firm') {
			$params['firms'] = $em->getRepository('VidalVeterinarBundle:Company')->findByQuery($q);
		}

		return $params;
	}

	/**
	 * @Route("/veterinar/searche", name="v_searche", options={"expose"=true})
	 *
	 * @Template("VidalVeterinarBundle:Search:searche.html.twig")
	 */
	public function searcheAction(Request $request)
	{
		$em          = $this->getDoctrine()->getManager('veterinar');
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
			return $this->render('VidalVeterinarBundle:Search:searche_too_short.html.twig', $params);
		}

		# для некоторых типов запроса надо найти основание слова (чтоб не учитывать окончание)
		if (in_array($t, array('all', 'molecule', 'atc', 'nosology', 'clphgroup', 'phthgroup'))) {
			$q = $this->get('lingua.service')->stem_string($q);
		}

		if ($t == 'all' || $t == 'product') {
			$products                     = $em->getRepository('VidalVeterinarBundle:Product')->findByQuery($q, $badIncluded);
			$paginator                    = $this->get('knp_paginator');
			$pagination                   = $paginator->paginate($products, $p, self::PRODUCTS_PER_PAGE);
			$params['productsPagination'] = $pagination;

			if ($pagination->getTotalItemCount()) {
				$productIds          = $this->getProductIds($pagination);
				$params['companies'] = $em->getRepository('VidalVeterinarBundle:Company')->findByProducts($productIds);;
				$params['pictures'] = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
			}
		}

		# на следующих страницах отображаются только препараты
		if ($p == 1) {
			# поиск по активному веществу
			if ($t == 'all' || $t == 'molecule') {
				$params['molecules'] = $em->getRepository('VidalVeterinarBundle:Molecule')->findByQuery($q);
			}

			# поиск по показаниям (МКБ-10) - Nozology
			if ($t == 'all' || $t == 'nosology') {
				$params['nozologies'] = $em->getRepository('VidalVeterinarBundle:Nozology')->findByQuery($q);
			}

			# поиск по АТХ коду
			if ($t == 'atc') {
				$params['atcCodes'] = $em->getRepository('VidalVeterinarBundle:ATC')->findByQuery($q);
				$params['atcTree']  = true;
			}

			# поиск по производителю
			if ($t == 'firm') {
				$params['firms'] = $em->getRepository('VidalVeterinarBundle:Company')->findByQuery($q);
			}

			# поиск по клиннико-фармакологической группе
			if ($t == 'clphgroup') {
				$params['clphgroups'] = $em->getRepository('VidalVeterinarBundle:Document')->findClPhGroupsByQuery($q);
			}

			# поиск по фармако-терапевтической группе
			if ($t == 'phthgroup') {
				$params['phthgroups'] = $em->getRepository('VidalVeterinarBundle:Product')->findPhThGroupsByQuery($q);
			}
		}

		return $params;
	}

	/**
	 * @Route("/veterinar/search/indic", name="v_searche_indic", options={"expose":true})
	 *
	 * @Template("VidalVeterinarBundle:Search:searche_indic.html.twig")
	 */
	public function searcheIndicAction(Request $request)
	{
		$em          = $this->getDoctrine()->getManager('veterinar');
		$contraCodes = $nozologyCodes = null;
		$params      = array();

		if ($request->query->has('nozology')) {
			$nozologyCodes = explode('-', $request->query->get('nozology'));

			if (empty($nozologyCodes)) {
				$params['noNozology'] = true;

				return $params;
			}

			$params['nozologies'] = $em->getRepository('VidalVeterinarBundle:Nozology')->findByCodes($nozologyCodes);

			if ($request->query->has('contra')) {
				$contraCodes                 = explode('-', $request->query->get('contra'));
				$params['contraindications'] = $em->getRepository('VidalVeterinarBundle:Contraindication')->findByCodes($contraCodes);
			}

			$documentIds = $em->getRepository('VidalVeterinarBundle:Document')
				->findIdsByNozologyContraCodes($nozologyCodes, $contraCodes);

			if (!empty($documentIds)) {
				$products = $em
					->getRepository('VidalVeterinarBundle:Product')
					->findByDocumentIDs($documentIds);

				if (!empty($products)) {
					$pagination = $this->get('knp_paginator')->paginate(
						$products,
						$request->query->get('p', 1),
						self::PRODUCTS_PER_PAGE
					);

					$productIds = $this->getProductIds($products);

					$params['productsPagination']   = $pagination;
					$params['companies']            = $em->getRepository('VidalVeterinarBundle:Company')->findByProducts($productIds);
					$params['pictures'] = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
				}
			}
		}

		return $params;
	}

	/**
	 * Отображение препаратов или бадов по букве алфавита
	 * @Route("/veterinar/search/letter", name="v_searche_letter")
	 *
	 * @Template("VidalVeterinarBundle:Search:searche_letter.html.twig")
	 */
	public function searcheLetterAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager('veterinar');
		$t  = $request->query->get('t', 'p'); // тип препараты-бады-вместе
		$p  = $request->query->get('p', 1); // номер страницы
		$l  = $request->query->get('l', null); // буква
		$n  = $request->query->has('n'); // только безрецептурные препараты

		$params = array(
			't'    => $t,
			'p'    => $p,
			'l'    => $l,
			'n'    => $n,
			'menu' => 'drugs',
		);

		$repo = $this->getDoctrine()->getManager('drug')->getRepository('VidalDrugBundle:Document');
		$repo = $em->getRepository('VidalVeterinarBundle:Document');

		if ($l != null) {
			$paginator  = $this->get('knp_paginator');
			$pagination = $paginator->paginate(
				$em->getRepository('VidalVeterinarBundle:Product')->getQueryByLetter($l, $t, $n),
				$p,
				self::PRODUCTS_PER_PAGE
			);

			$products             = $pagination->getItems();
			$params['pagination'] = $pagination;

			if (!empty($products)) {
				$productIds = array();

				foreach ($products as $product) {
					$productIds[] = $product->getProductID();
				}

				$params['products']    = $products;
				$params['indications'] = $em->getRepository('VidalVeterinarBundle:Document')->findIndicationsByProductIds($productIds);
				$params['companies']   = $em->getRepository('VidalVeterinarBundle:Company')->findByProducts($productIds);
				$params['pictures']    = $em->getRepository('VidalVeterinarBundle:Picture')->findByProductIds($productIds);
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
	 * Функция генерации дерева с кодами АТС
	 * @Route("/tree/atc", name="tree_atc")
	 */
	public function treeAtcAction()
	{
		$em         = $this->getDoctrine()->getManager('veterinar');
		$atcCodes   = $em->getRepository('VidalVeterinarBundle:ATC')->findAll();
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

		return $this->render('VidalVeterinarBundle:Search:tree_atc_generator.html.twig', array(
			'atcGrouped' => $atcGrouped,
		));
	}
}
