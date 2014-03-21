<?php

namespace Vidal\DrugBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class DrugsController extends Controller
{
	const PHARM_PER_PAGE = 50;

	/**
	 * Препараты по коду АТХ
	 *
	 * @Route("drugs/atc/{ATCCode}", name="atc_item", options={"expose":true})
	 * @Route("poisk_preparatov/lat_{ATCCode}.{ext}", name="atc_item_old", defaults={"ext"="htm"})
	 * @Template("VidalDrugBundle:Drugs:atc_item.html.twig")
	 */
	public function atcItemAction($ATCCode)
	{
		$em  = $this->getDoctrine()->getManager('drug');
		$atc = $em->getRepository('VidalDrugBundle:ATC')->findOneByATCCode($ATCCode);

		if (!$atc) {
			throw $this->createNotFoundException();
		}

		# все продукты по ATC-коду и отсеиваем дубли
		$productsRaw = $em->getRepository('VidalDrugBundle:Product')->findByATCCode($ATCCode);
		$products    = array();

		if (empty($productsRaw)) {
			return array('atc' => $atc);
		}

		for ($i = 0; $i < count($productsRaw); $i++) {
			$key = $productsRaw[$i]['ProductID'];
			if (!isset($productsRaw[$key])) {
				$products[$key] = $productsRaw[$i];
			}
		}

		# надо разбить на те, что с описанием(2,5) и остальные
		$products1  = array();
		$products2  = array();
		$productIds = array();

		foreach ($products as $id => $product) {
			if ($product['ArticleID'] == 2 || $product['ArticleID'] == 5) {
				$key = $product['DocumentID'];
				if (!isset($products1[$key])) {
					$products1[$key] = $product;
				}
			}
			else {
				$products2[] = $product;
			}

			$productIds[] = $id;
		}

		return array(
			'atc'       => $atc,
			'products1' => $products1,
			'products2' => $products2,
			'companies' => $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds),
			'pictures'  => $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds)
		);
	}

	/**
	 * Дерево АТХ
	 *
	 * @Route("drugs/atc", name="atc")
	 * @Template("VidalDrugBundle:Drugs:atc.html.twig")
	 */
	public function atcAction()
	{
		$params = array(
			'menu_drugs' => 'atc',
		);

		return $params;
	}

	/**
	 * [AJAX] Подгрузка дерева ATC
	 *
	 * @Route("drugs/atc-ajax", name="atc_ajax", options={"expose":true})
	 */
	public function atcAjaxAction()
	{
		$html = $this->renderView('VidalDrugBundle:Search:tree_atc_generated.html.twig');

		return new JsonResponse($html);
	}

	/**
	 * Препараты по КФУ
	 *
	 * @Route("drugs/kfu/{url}", name="kfu_item", options={"expose":true})
	 * @Template("VidalDrugBundle:Drugs:kfu_item.html.twig")
	 */
	public function kfuItemAction($url)
	{
		$em  = $this->getDoctrine()->getManager('drug');
		$kfu = $em->getRepository('VidalDrugBundle:ClinicoPhPointers')->findOneByUrl($url);

		if (!$kfu) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'menu_drugs' => 'kfu',
			'kfu'        => $kfu,
		);

		$products = $em->getRepository('VidalDrugBundle:Product')->findByKfu($kfu);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['products']  = $products;
			$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds);
		}

		return $params;
	}

	/**
	 * Дерево КФУ
	 *
	 * @Route("drugs", name="drugs")
	 * @Route("drugs/kfu", name="kfu")
	 * @Template("VidalDrugBundle:Drugs:kfu.html.twig")
	 */
	public function kfuAction()
	{
		$params = array(
			'menu_drugs' => 'kfu',
		);

		return $params;
	}

	/**
	 * [AJAX] Подгрузка дерева КФУ
	 *
	 * @Route("drugs/kfu-ajax", name="kfu_ajax", options={"expose":true})
	 */
	public function kfuAjaxAction()
	{
		$html = $this->renderView('VidalDrugBundle:Drugs:kfu_generated.html.twig');

		return new JsonResponse($html);
	}

	/**
	 * Функция генерации дерева с кодами КФУ
	 *
	 * @Route("drugs/kfu-generator", name="kfu_generator")
	 * @Template("VidalDrugBundle:Drugs:kfu_generator.html.twig")
	 */
	public function kfuGeneratorAction()
	{
		$em    = $this->getDoctrine()->getManager('drug');
		$repo  = $em->getRepository('VidalDrugBundle:ClinicoPhPointers');
		$codes = $repo->findForTree();

		# надо сгруппировать по родителю (запихпуть в list родителя дочерние)
		for ($i = 14; $i > 0; $i = $i - 3) {
			foreach ($codes as $codeValue => $code) {
				if (strlen($codeValue) == $i) {
					$key = substr($codeValue, 0, -3);
					if (isset($codes[$key]) && strlen($codeValue) > strlen($key)) {
						$codes[$key]['list'][$codeValue] = $code;
					}
				}
			}
		}

		$grouped = array();

		foreach ($codes as $codeValue => $code) {
			if (strlen($codeValue) == 2) {
				$grouped[] = $code;
			}
		}

		return array('codes' => $grouped);
	}

	/**
	 * Список компаний
	 *
	 * @Route("drugs/pharm-groups", name="pharm")
	 * @Template("VidalDrugBundle:Drugs:pharm.html.twig")
	 */
	public function pharmAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager('drug');
		$q  = $request->query->get('q', null);
		$l  = $request->query->get('l', null);
		$p  = $request->query->get('p', 1);

		//		$companies = $em->getRepository('VidalDrugBundle:PhThGroups')->getQuery()->getResult();
		//		$letters   = array();
		//		foreach ($companies as $company) {
		//			$letter = mb_strtoupper(mb_substr($company->getName(), 0, 1, 'utf-8'), 'utf-8');
		//			if (!isset($letters[$letter])) {
		//				$letters[$letter] = '';
		//			}
		//		}
		//		var_dump($letters);
		//		exit;

		if ($l) {
			$query = $em->getRepository('VidalDrugBundle:PhThGroups')->getQueryByLetter($l);
		}
		elseif ($q) {
			$query = $em->getRepository('VidalDrugBundle:PhThGroups')->findByQueryString($q);
		}
		else {
			$query = $em->getRepository('VidalDrugBundle:PhThGroups')->getQuery();
		}

		$params = array(
			'menu_drugs' => 'pharm',
			'title'      => 'Фирмы-производители',
			'q'          => $q,
			'l'          => $l,
			'pagination' => $this->get('knp_paginator')->paginate($query, $p, self::PHARM_PER_PAGE),
		);

		return $params;
	}

	/**
	 * Список препаратов по фармако-терапевтической группе
	 *
	 * @Route("drugs/pharm-group/{id}", name="pharm_item", defaults={"id":"\d+"})
	 * @Template("VidalDrugBundle:Drugs:pharm_item.html.twig")
	 */
	public function pharmItemAction($id)
	{
		$em        = $this->getDoctrine()->getManager('drug');
		$phthgroup = $em->getRepository('VidalDrugBundle:PhThGroups')->findById($id);

		if ($phthgroup === null) {
			throw $this->createNotFoundException();
		}

		$params = array('phthgroup' => $phthgroup);

		$products = $em->getRepository('VidalDrugBundle:Product')->findByPhThGroup($id);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['products']  = $products;
			$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds);
		}

		return $params;
	}

	/**
	 * Список препаратов и активных веществ по показанию (Nozology)
	 *
	 * @Route("drugs/nosology/{Code}", name="nosology_item", options={"expose":true})
	 * @Route("poisk_preparatov/lno_{Code}", name="nosology_item_old")
	 * @Template("VidalDrugBundle:Drugs:nosology_item.html.twig")
	 */
	public function nosologyItemAction(Request $request, $Code)
	{
		$em        = $this->getDoctrine()->getManager('drug');
		$routeName = $request->get('_route');

		if ($routeName == 'nosology_item_old') {
			if ($pos = strpos($Code, '.html')) {
				$Code = substr($Code, 0, $pos);
			}
			elseif ($pos = strpos($Code, '.htm')) {
				$Code = substr($Code, 0, $pos);
			}
		}

		$nozology = $em->getRepository('VidalDrugBundle:Nozology')->findByCode($Code);

		if ($nozology === null) {
			throw $this->createNotFoundException();
		}

		$documents = $em->getRepository('VidalDrugBundle:Document')->findByNozologyCode($Code);
		$params    = array('nozology' => $nozology);

		if (!empty($documents)) {
			$params['molecules'] = $em->getRepository('VidalDrugBundle:Molecule')->findByDocuments1($documents);
			$products1           = $em->getRepository('VidalDrugBundle:Product')->findByDocuments25($documents);
			$products2           = $em->getRepository('VidalDrugBundle:Product')->findByDocuments4($documents);
			$products            = array();

			# надо слить продукты, исключая повторения и отсортировать по названию
			foreach ($products1 as $id => $product) {
				$products[] = $product;
			}
			foreach ($products2 as $id => $product) {
				if (!isset($products1[$id])) {
					$products[] = $product;
				}
			}
			usort($products, function ($a, $b) {
				return strcmp($a['RusName'], $b['RusName']);
			});

			$productIds          = $this->getProductIds($products);
			$params['products']  = $products;
			$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds);
		}

		return $params;
	}

	/**
	 * Список нозологических указателей
	 *
	 * @Route("drugs/nosology", name="nosology")
	 * @Template("VidalDrugBundle:Drugs:nosology.html.twig")
	 */
	public function nosologyAction()
	{
		return array('menu_drugs' => 'nosology');
	}

	/**
	 * [AJAX] Подгрузка дерева Нозологических указателей
	 *
	 * @Route("drugs/nosology-ajax", name="nosology_ajax", options={"expose":true})
	 */
	public function nosologyAjaxAction()
	{
		$html = $this->renderView('VidalDrugBundle:Drugs:nosology_generated.html.twig');

		return new JsonResponse($html);
	}

	/**
	 * Функция генерации дерева нозологических указателей
	 *
	 * @Route("drugs/nosology-generator", name="nosology_generator")
	 * @Template("VidalDrugBundle:Drugs:nosology_generator.html.twig")
	 */
	public function nosologyGeneratorAction()
	{
		$em         = $this->getDoctrine()->getManager('drug');
		$nozologies = $em->getRepository('VidalDrugBundle:Nozology')->findForTree();

		$finds = array();

		$i = 0;
		foreach ($nozologies as $code => &$n) {
			$n['i']  = $i;
			$finds[] = $n;
			$i++;
		}

		# надо сгруппировать по родителю (запихпуть в list родителя дочерние)
		for ($i = 3; $i > 0; $i--) {
			foreach ($nozologies as $code => &$nozology) {
				if ($nozology['Level'] == $i) {
					# надо найти родителя
					$prev  = false;
					$minus = 1;
					while (!$prev) {
						$prevIndex = $nozology['i'] - $minus;
						if ($finds[$prevIndex]['Level'] < $nozology['Level']) {
							$prev = $finds[$prevIndex];
						}
						$minus++;
					}
					$prevCode                        = $prev['Code'];
					$nozologies[$prevCode]['list'][] = $nozology;
				}
			}
		}

		# надо взять только верхний уровень
		$grouped = array();

		foreach ($nozologies as $code => $nozology) {
			if ($nozology['Level'] == 0) {
				$grouped[] = $nozology;
			}
		}

		return array('codes' => $grouped);
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
