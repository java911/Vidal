<?php

namespace Vidal\DrugBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lsw\SecureControllerBundle\Annotation\Secure;

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
		$products   = $em->getRepository('VidalDrugBundle:Product')->findByATCCode($ATCCode);
		$productIds = $this->getProductIds($products);

		return array(
			'atc'       => $atc,
			'products'  => $products,
			'companies' => $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds),
			'pictures'  => $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds, date('Y')),
			'infoPages' => $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products),
			'title'     => $atc->getRusName() . ' - ' . $atc . ' | АТХ',
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
			'title'      => 'АТХ',
		);

		return $params;
	}

	/**
	 * [AJAX] Подгрузка дерева ATC
	 *
	 * @Route("drugs/atc-ajax", name="atc_ajax", options={"expose":true})
	 */
	public function atcAjaxAction(Request $request)
	{
		if ($request->request->has('root')) {
			$file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Generated' . DIRECTORY_SEPARATOR . 'atc.json';
			$json = json_decode(file_get_contents($file), true);
			$root = $request->request->get('root');
			$data = $json[$root]['children'];

			return new JsonResponse($data);
		}

		return new JsonResponse();
	}

	/**
	 * Функция генерации дерева с кодами ATC
	 *
	 * @Route("drugs/atc-generator", name="atc_generator")
	 * @Template("VidalDrugBundle:Drugs:atc_generator.html.twig")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function atcGeneratorAction()
	{
		$em    = $this->getDoctrine()->getManager('drug');
		$repo  = $em->getRepository('VidalDrugBundle:ATC');
		$codes = $repo->findForTree();

		return array('codes' => $codes);
	}

	/**
	 * Препараты по КФУ
	 *
	 * @Route("drugs/clinic-group/{id}", name="kfu_item", options={"expose":true})
	 * @Template("VidalDrugBundle:Drugs:kfu_item.html.twig")
	 */
	public function kfuItemAction($id)
	{
		$em  = $this->getDoctrine()->getManager('drug');
		$kfu = $em->getRepository('VidalDrugBundle:ClinicoPhPointers')->findOneById($id);

		if (!$kfu) {
			throw $this->createNotFoundException();
		}

		$params = array(
			'menu_drugs' => 'kfu',
			'kfu'        => $kfu,
			'title'      => $kfu . ' | Клинико-фармакологические указатели',
		);

		$products = $em->getRepository('VidalDrugBundle:Product')->findByKfu($kfu);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['products']  = $products;
			$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds, date('Y'));
			$params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
		}

		return $params;
	}

	/**
	 * Дерево КФУ
	 *
	 * @Route("drugs", name="drugs")
	 * @Route("drugs/clinic-groups", name="kfu")
	 * @Template("VidalDrugBundle:Drugs:kfu.html.twig")
	 */
	public function kfuAction()
	{
		$params = array(
			'menu_drugs' => 'kfu',
			'title'      => 'Клинико-фармакологические указатели',
		);

		return $params;
	}

	/**
	 * [AJAX] Подгрузка дерева КФУ
	 *
	 * @Route("drugs/kfu-ajax", name="kfu_ajax", options={"expose":true})
	 */
	public function kfuAjaxAction(Request $request)
	{
		if ($request->request->has('root')) {
			$file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Generated' . DIRECTORY_SEPARATOR . 'kfu.json';
			$json = json_decode(file_get_contents($file), true);
			$root = $request->request->get('root');
			$data = $json[$root]['children'];

			return new JsonResponse($data);
		}

		return new JsonResponse();
	}

	/**
	 * Функция генерации дерева с кодами КФУ
	 *
	 * @Route("drugs/kfu-generator", name="kfu_generator")
	 * @Template("VidalDrugBundle:Drugs:kfu_generator.html.twig")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function kfuGeneratorAction()
	{
		$em    = $this->getDoctrine()->getManager('drug');
		$repo  = $em->getRepository('VidalDrugBundle:ClinicoPhPointers');
		$codes = $repo->findForTree();

		return array('codes' => $codes);
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
			'title'      => 'Фармако-терапевтические группы',
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

		$params = array(
			'phthgroup' => $phthgroup,
			'title'     => $phthgroup['Name'] . ' | Фармако-терапевтические группы',
		);

		$products = $em->getRepository('VidalDrugBundle:Product')->findByPhThGroup($id);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['products']  = $products;
			$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds, date('Y'));
			$params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
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
			$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds, date('Y'));
			$params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
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
	public function nosologyAjaxAction(Request $request)
	{
		if ($request->request->has('root')) {
			$file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Generated' . DIRECTORY_SEPARATOR . 'nosology.json';
			$json = json_decode(file_get_contents($file), true);
			$root = $request->request->get('root');
			$data = $json[$root]['children'];

			return new JsonResponse($data);
		}

		return new JsonResponse();
	}

	/**
	 * Функция генерации дерева нозологических указателей
	 *
	 * @Route("drugs/nosology-generator", name="nosology_generator")
	 * @Template("VidalDrugBundle:Drugs:nosology_generator.html.twig")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function nosologyGeneratorAction()
	{
		$em         = $this->getDoctrine()->getManager('drug');
		$nozologies = $em->getRepository('VidalDrugBundle:Nozology')->findForTree();

		return array('codes' => $nozologies);
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

	private function strip($string)
	{
		$pat = array('/<sup>(.*?)<\/sup>/i', '/<sub>(.*?)<\/sub>/i', '/&amp;/');
		$rep = array('', '', '&');

		return preg_replace($pat, $rep, $string);
	}
}
