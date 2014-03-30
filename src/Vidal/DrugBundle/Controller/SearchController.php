<?php

namespace Vidal\DrugBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
		$bad    = $request->query->has('bad');
		$params = array(
			'q'     => $q,
			't'     => $t,
			'title' => 'Поиск',
		);

		# поисковый запрос не может быть меньше 2
		if (mb_strlen($q, 'UTF-8') < 2) {
			return $this->render('VidalDrugBundle:Search:search_too_short.html.twig', $params);
		}

		# для некоторых типов запроса надо найти основание слова (чтоб не учитывать окончание)
		if (in_array($t, array('all', 'molecule', 'atc'))) {
			$q = $this->get('lingua.service')->stem_string($q);
		}

		if ($t == 'all' || $t == 'product') {
			$products = $em->getRepository('VidalDrugBundle:Product')->findByQuery($q, $bad);

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
		$params      = array(
			'q'     => $q,
			't'     => $t,
			'title' => 'Расширенный поиск',
		);

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

		return $params;
	}

	/**
	 * @Route("/search/indic", name="searche_indic", options={"expose":true})
	 *
	 * @Template("VidalDrugBundle:Search:searche_indic.html.twig")
	 */
	public function searcheIndicAction(Request $request)
	{
		$em          = $this->getDoctrine()->getManager('drug');
		$contraCodes = $nozologyCodes = null;
		$params      = array(
			'title' => 'Поиск по показаниям/противопоказаниям',
		);

		if ($request->query->has('nozology')) {
			$nozologyCodes = explode('-', $request->query->get('nozology'));

			if (empty($nozologyCodes)) {
				$params['noNozology'] = true;

				return $params;
			}

			$params['nozologies'] = $em->getRepository('VidalDrugBundle:Nozology')->findByCodes($nozologyCodes);

			if ($request->query->has('contra')) {
				$contraCodes                 = explode('-', $request->query->get('contra'));
				$params['contraindications'] = $em->getRepository('VidalDrugBundle:Contraindication')->findByCodes($contraCodes);
			}

			$documentIds = $em->getRepository('VidalDrugBundle:Document')
				->findIdsByNozologyContraCodes($nozologyCodes, $contraCodes);

			if (!empty($documentIds)) {
				$products = $em
					->getRepository('VidalDrugBundle:Product')
					->findByDocumentIDs($documentIds);

				if (!empty($products)) {
					$pagination = $this->get('knp_paginator')->paginate(
						$products,
						$request->query->get('p', 1),
						self::PRODUCTS_PER_PAGE
					);

					$productIds = $this->getProductIds($products);

					$params['productsPagination'] = $pagination;
					$params['companies']          = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
					$params['pictures']           = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds);
				}
			}
		}

		return $params;
	}

	/**
	 * Отображение препаратов или бадов по букве алфавита
	 * @Route("/search/letter", name="searche_letter")
	 *
	 * @Template("VidalDrugBundle:Search:searche_letter.html.twig")
	 */
	public function searcheLetterAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager('drug');
		$t  = $request->query->get('t', 'p'); // тип препараты-бады-вместе
		$p  = $request->query->get('p', 1); // номер страницы
		$l  = $request->query->get('l', null); // буква
		$n  = $request->query->has('n'); // только безрецептурные препараты

		$params = array(
			't'     => $t,
			'p'     => $p,
			'l'     => $l,
			'n'     => $n,
			'menu'  => 'drugs',
			'title' => 'Поиск по алфавиту',
		);

		if ($l != null) {
			$paginator  = $this->get('knp_paginator');
			$pagination = $paginator->paginate(
				$em->getRepository('VidalDrugBundle:Product')->getQueryByLetter($l, $t, $n),
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
				$params['indications'] = $em->getRepository('VidalDrugBundle:Document')->findIndicationsByProductIds($productIds);
				$params['companies']   = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
				$params['pictures']    = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds);
			}
		}

		return $params;
	}

	/**
	 * @Route("/searche/atc-full", name="atc_full", options={"expose":true})
	 */
	public function atcFullAction()
	{
		$html = $this->renderView('VidalDrugBundle:Search:atc_full.html.twig');

		return new JsonResponse($html);
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
}
