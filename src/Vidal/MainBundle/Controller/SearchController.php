<?php

namespace Vidal\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

		if ($t == 'all' || $t == 'product') {
			$products                     = $em->getRepository('VidalMainBundle:Product')->findByQuery($q);
			$paginator                    = $this->get('knp_paginator');
			$pagination                   = $paginator->paginate($products, $p, self::PRODUCTS_PER_PAGE);
			$params['productsPagination'] = $pagination;

			if ($pagination->getTotalItemCount()) {
				$productIds = array();

				foreach ($pagination as $product) {
					$productIds[] = $product['ProductID'];
				}

				# надо получить компании и сгруппировать их по продукту
				$companies        = $em->getRepository('VidalMainBundle:Company')->findByProducts($productIds);
				$productCompanies = array();

				foreach ($companies as $company) {
					$key = $company['ProductID'];
					isset($productCompanies[$key])
						? $productCompanies[$key][] = $company
						: $productCompanies[$key] = array($company);
				}

				$params['companies'] = $productCompanies;
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

	}
}
