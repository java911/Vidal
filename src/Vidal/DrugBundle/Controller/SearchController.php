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
	const PRODUCTS_PER_PAGE = 20;

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
			$productsRaw = $em->getRepository('VidalDrugBundle:Product')->findByQuery($q, $bad);

			# если включаем бады, то их надо в отдельную группу
			if ($bad) {
				$products = array();
				$bads     = array();
				foreach ($productsRaw as $product) {
					$product['ProductTypeCode'] == 'BAD'
						? $bads[] = $product
						: $products[] = $product;
				}
				if (count($bads)) {
					$badIds                  = $this->getProductIds($bads);
					$params['bads']          = $bads;
					$params['bad_companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($badIds);
					$params['bad_pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($badIds);
					$params['bad_infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($bads);
				}
			}
			else {
				$products = $productsRaw;
			}

			$paginator                    = $this->get('knp_paginator');
			$pagination                   = $paginator->paginate($products, $p, self::PRODUCTS_PER_PAGE);
			$params['productsPagination'] = $pagination;

			if ($pagination->getTotalItemCount()) {
				$productIds          = $this->getProductIds($pagination);
				$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
				$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds);
				$params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($pagination);
			}
		}

		# поиск по активному веществу
		if (($t == 'all' || $t == 'molecule') && $p == 1) {
			$params['molecules'] = $em->getRepository('VidalDrugBundle:Molecule')->findByQuery($q);
		}

		# поиск по АТХ коду
		if ($t == 'atc') {
			$qUpper             = mb_strtoupper($q, 'utf-8');
			$params['atcCodes'] = $em->getRepository('VidalDrugBundle:ATC')->findByQuery($qUpper);
		}

		# поиск по производителю
		if ($t == 'firm') {
			$params['firms'] = $em->getRepository('VidalDrugBundle:Company')->findByQuery($q);
		}

		# поиск по заболеванию (это статьи и синонимы)
		if ($t == 'all' || $t == 'disease') {
			$articles = $em->getRepository('VidalDrugBundle:Article')->findByQuery($q);
			# если есть БАДы, то исключаем дублирующие их статьи
			if (isset($params['bads']) && !empty($articles)) {
				$articles = $this->excludeBads($articles, $params['bads']);
			}
			$params['articles'] = $articles;
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
		$em  = $this->getDoctrine()->getManager('drug');
		$q   = $request->query->get('q', ''); # поисковый запрос
		$q   = trim($q);
		$t   = $request->query->get('t', 'all'); # тип запроса из селект-бокса
		$p   = $request->query->get('p', 1); # номер страницы
		$bad = $request->query->has('b'); # включать ли бады

		$params = array(
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
			$productsRaw = $em->getRepository('VidalDrugBundle:Product')->findByQuery($q, $bad);

			# если включаем бады, то их надо в отдельную группу
			if ($bad) {
				$products = array();
				$bads     = array();
				foreach ($productsRaw as $product) {
					$product['ProductTypeCode'] == 'BAD'
						? $bads[] = $product
						: $products[] = $product;
				}
				if (count($bads)) {
					$badIds                  = $this->getProductIds($bads);
					$params['bads']          = $bads;
					$params['bad_companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($badIds);
					$params['bad_pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($badIds);
					$params['bad_infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($bads);
				}
			}
			else {
				$products = $productsRaw;
			}

			$paginator                    = $this->get('knp_paginator');
			$pagination                   = $paginator->paginate($products, $p, self::PRODUCTS_PER_PAGE);
			$params['productsPagination'] = $pagination;

			if ($pagination->getTotalItemCount()) {
				$productIds          = $this->getProductIds($pagination);
				$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
				$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds);
				$params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($pagination);
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
				$qUpper             = mb_strtoupper($q, 'utf-8');
				$params['atcCodes'] = $em->getRepository('VidalDrugBundle:ATC')->findByQuery($qUpper);
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

			# поиск по заболеванию (это статьи и синонимы)
			if ($t == 'all' || $t == 'disease') {
				$articles = $em->getRepository('VidalDrugBundle:Article')->findByQuery($q);
				# если есть БАДы, то исключаем дублирующие их статьи
				if (isset($params['bads']) && !empty($articles)) {
					$articles = $this->excludeBads($articles, $params['bads']);
				}
				$params['articles'] = $articles;
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
	 * @Route("/search/disease", name="searche_disease")
	 * @Template("VidalDrugBundle:Search:searche_disease.html.twig")
	 */
	public function diseaseAction(Request $request)
	{
		$l  = $request->query->get('l', null);
		$q  = $request->query->get('q', null);
		$em = $this->getDoctrine()->getManager('drug');

		$params = array(
			'title' => 'Список болезней по алфавиту',
			'l'     => $l,
			'q'     => $q,
		);

		if ($l) {
			$articles           = $em->getRepository('VidalDrugBundle:Article')->findDisease($l);
			$params['articles'] = $this->highlight($articles, $l);
		}
		elseif ($q) {
			$q                  = trim($q);
			$params['articles'] = $em->getRepository('VidalDrugBundle:Article')->findByQuery($q);
		}
		else {

		}

		return $params;
	}

	/**
	 * @Route("/patsientam/spisok-boleznei-po-alfavitu/")
	 */
	public function r1()
	{
		return $this->redirect($this->generateUrl('disease'), 301);
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

	private function highlight($articles, $l)
	{
		foreach ($articles as &$article) {
			# подсвечиваем заголовок статьи
			$words = explode(' ', $article['title']);
			$title = '';
			foreach ($words as $word) {
				$firstLetter = mb_strtoupper(mb_substr($word, 0, 1, 'utf-8'), 'utf-8');
				$title[]     = $firstLetter == $l ? '<b>' . $word . '</b>' : $word;
			}
			$article['title'] = implode(' ', $title);

			# подсвечиваем синонимы
			$words = explode(' ', $article['synonym']);
			$title = '';
			foreach ($words as $word) {
				$firstLetter = mb_strtoupper(mb_substr($word, 0, 1, 'utf-8'), 'utf-8');
				$title[]     = $firstLetter == $l ? '<b>' . $word . '</b>' : $word;
			}
			$article['synonym'] = implode(' ', $title);
		}

		return $articles;
	}

	private function excludeBads($articlesRaw, $bads)
	{
		$badNames = array();
		$articles = array();

		foreach ($bads as $bad) {
			$badNames[] = $this->stripLower($bad['RusName']);
		}

		foreach ($articlesRaw as $article) {
			$title = $this->stripLower($article['title']);

			if (!in_array($title, $badNames)) {
				$articles[] = $article;
			}
		}

		return $articles;
	}

	private function stripLower($string)
	{
		$pat = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i', '/&amp;/', '/®/');
		$rep = array('', '', '&', '');

		return mb_strtolower(preg_replace($pat, $rep, $string), 'utf-8');
	}
}
