<?php

namespace Vidal\DrugBundle\Controller;

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
	 * @Template("VidalDrugBundle:Search:search.html.twig")
	 */
	public function searchAction(Request $request)
	{
		$em     = $this->getDoctrine()->getManager('drug');
		$q      = $request->query->get('q', ''); # поисковый запрос
		$q      = trim($q);
		$t      = $request->query->get('t', 'all'); # тип запроса из селект-бокса
		$p      = $request->query->get('p', 1); # номер страницы
		$params = array('q' => $q, 't' => $t);

		# поисковый запрос не может быть меньше 2
		if (mb_strlen($q, 'UTF-8') < 2) {
			return $this->render('VidalDrugBundle:Search:search_too_short.html.twig', $params);
		}

		# для некоторых типов запроса надо найти основание слова (чтоб не учитывать окончание)
		if (in_array($t, array('all', 'molecule', 'atc'))) {
			$q = $this->get('lingua.service')->stem_string($q);
		}

		if ($t == 'all' || $t == 'product') {
			$products = $em->getRepository('VidalDrugBundle:Product')->findByQuery($q);

			$paginator                    = $this->get('knp_paginator');
			$pagination                   = $paginator->paginate($products, $p, self::PRODUCTS_PER_PAGE);
			$params['productsPagination'] = $pagination;

			if ($pagination->getTotalItemCount()) {
				$productIds          = $this->getProductIds($pagination);
				$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
				$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds);
			}
		}

		# поиск по активному веществу
		if (($t == 'all' || $t == 'molecule') && $p == 1) {
			$params['molecules'] = $em->getRepository('VidalDrugBundle:Molecule')->findByQuery($q);
		}

		# поиск по АТХ коду
		if ($t == 'atc') {
			$params['atcCodes'] = $em->getRepository('VidalDrugBundle:ATC')->findByQuery($q);
		}

		# поиск по производителю
		if ($t == 'firm') {
			$params['firms'] = $em->getRepository('VidalDrugBundle:Company')->findByQuery($q);
		}

		return $params;
	}

	/**
	 * @Route("/searche", name="searche", options={"expose"=true})
	 *
	 * @Template("VidalDrugBundle:Search:searche.html.twig")
	 */
	public function searcheAction(Request $request)
	{
		$em          = $this->getDoctrine()->getManager('drug');
		$q           = $request->query->get('q', ''); # поисковый запрос
		$q           = trim($q);
		$t           = $request->query->get('t', 'all'); # тип запроса из селект-бокса
		$p           = $request->query->get('p', 1); # номер страницы
		$badIncluded = $request->query->has('b'); # включать ли бады
		$params      = array('q' => $q, 't' => $t);
		$hasFilter   = $request->query->has('nozology') || $request->query->has('contra');

		if ($hasFilter) {
			$params['filtered']['status'] = true;

			if ($request->query->has('nozology')) {
				$nozologyCodes                    = explode('-', $request->query->get('nozology'));
				$params['filtered']['nozologies'] = $em->getRepository('VidalDrugBundle:Nozology')
					->findByCodes($nozologyCodes);
			}
			else {
				$nozologyCodes = null;
			}

			if ($request->query->has('contra')) {
				$contraCodes                             = explode('-', $request->query->get('contra'));
				$params['filtered']['contraindications'] = $em->getRepository('VidalDrugBundle:Contraindication')
					->findByCodes($contraCodes);
			}
			else {
				$contraCodes = null;
			}

			if (empty($nozologyCodes)) {
				$params['filtered']['fail'] = true;
				return $params;
			}

			$documentIds = $em->getRepository('VidalDrugBundle:Document')
				->findIdsByNozologyContraCodes($nozologyCodes, $contraCodes);

			if (!empty($documentIds)) {
				$products = $em->getRepository('VidalDrugBundle:Product')->findByDocumentIDs($documentIds);

				if (!empty($products)) {
					$paginator  = $this->get('knp_paginator');
					$pagination = $paginator->paginate($products, $p, self::PRODUCTS_PER_PAGE);
					$productIds = $this->getProductIds($products);

					$params['filtered']['productsPagination'] = $pagination;
					$params['filtered']['companies']          = $em->getRepository('VidalDrugBundle:Company')
						->findByProducts($productIds);;
					$params['filtered']['pictures'] = $em->getRepository('VidalDrugBundle:Picture')
						->findByProductIds($productIds);
				}
			}
		}
		else {
			# поисковый запрос не может быть меньше 2
			if (empty($q)) {
				return $params;
			}
			elseif (mb_strlen($q, 'UTF-8') < 2) {
				return $this->render('VidalDrugBundle:Search:searche_too_short.html.twig', $params);
			}

			# для некоторых типов запроса надо найти основание слова (чтоб не учитывать окончание)
			if (in_array($t, array('all', 'molecule', 'atc', 'nosology', 'clphgroup', 'phthgroup'))) {
				$q = $this->get('lingua.service')->stem_string($q);
			}

			if ($t == 'all' || $t == 'product') {
				$products                     = $em->getRepository('VidalDrugBundle:Product')->findByQuery($q, $badIncluded);
				$paginator                    = $this->get('knp_paginator');
				$pagination                   = $paginator->paginate($products, $p, self::PRODUCTS_PER_PAGE);
				$params['productsPagination'] = $pagination;

				if ($pagination->getTotalItemCount()) {
					$productIds          = $this->getProductIds($pagination);
					$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);;
					$params['pictures'] = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds);
				}
			}

			# на следующих страницах отображаются только препараты
			if ($p == 1) {
				# поиск по активному веществу
				if ($t == 'all' || $t == 'molecule') {
					$params['molecules'] = $em->getRepository('VidalDrugBundle:Molecule')->findByQuery($q);
				}

				# поиск по показаниям (МКБ-10) - Nozology
				if ($t == 'all' || $t == 'nosology') {
					$params['nozologies'] = $em->getRepository('VidalDrugBundle:Nozology')->findByQuery($q);
				}

				# поиск по АТХ коду
				if ($t == 'atc') {
					$params['atcCodes'] = $em->getRepository('VidalDrugBundle:ATC')->findByQuery($q);
					$params['atcTree']  = true;
				}

				# поиск по производителю
				if ($t == 'firm') {
					$params['firms'] = $em->getRepository('VidalDrugBundle:Company')->findByQuery($q);
				}

				# поиск по клиннико-фармакологической группе
				if ($t == 'clphgroup') {
					$params['clphgroups'] = $em->getRepository('VidalDrugBundle:Document')->findClPhGroupsByQuery($q);
				}

				# поиск по фармако-терапевтической группе
				if ($t == 'phthgroup') {
					$params['phthgroups'] = $em->getRepository('VidalDrugBundle:Product')->findPhThGroupsByQuery($q);
				}
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
		$em         = $this->getDoctrine()->getManager('drug');
		$atcCodes   = $em->getRepository('VidalDrugBundle:ATC')->findAll();
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

		return $this->render('VidalDrugBundle:Search:tree_atc_generator.html.twig', array(
			'atcGrouped' => $atcGrouped,
		));
	}
}
