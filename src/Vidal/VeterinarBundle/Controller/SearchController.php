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

		# поиск по производителю
		if ($t == 'firm') {
			$params['firms'] = $em->getRepository('VidalVeterinarBundle:Company')->findByQuery($q);
		}

		return $params;
	}

	/**
	 * Отображение препаратов или бадов по букве алфавита
	 * @Route("/veterinar/letter", name="v_searche_letter")
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
}
