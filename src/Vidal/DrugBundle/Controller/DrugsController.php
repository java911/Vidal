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
	const KFG_PER_PAGE   = 50;

	/**
	 * Препараты по коду АТХ
	 *
	 * @Route("/drugs/atc/{ATCCode}/{search}", name="atc_item", options={"expose":true})
	 * @Template("VidalDrugBundle:Drugs:atc_item.html.twig")
	 */
	public function atcItemAction($ATCCode, $search = 0)
	{
		$em  = $this->getDoctrine()->getManager('drug');
		$atc = $em->getRepository('VidalDrugBundle:ATC')->findOneByATCCode($ATCCode);

		if (!$atc) {
			throw $this->createNotFoundException();
		}

		# все продукты по ATC-коду и отсеиваем дубли
		$products = $em->getRepository('VidalDrugBundle:Product')->findByATCCode($ATCCode);
		$params   = array(
			'atc'      => $atc,
			'products' => $products,
			'title'    => $atc->getRusName() . ' - ' . $atc . ' | АТХ',
		);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds, date('Y'));
			$params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
		}

		return $search ? $this->render('VidalDrugBundle:Drugs:search_atc_item.html.twig', $params) : $params;
	}

	/**
	 * Дерево АТХ
	 *
	 * @Route("/drugs", name="drugs")
	 * @Route("/drugs/atc", name="atc")
	 * @Template("VidalDrugBundle:Drugs:atc.html.twig")
	 */
	public function atcAction(Request $request)
	{
		$em      = $this->getDoctrine()->getManager('drug');
		$choices = $em->getRepository('VidalDrugBundle:ATC')->getChoices();
		$atcCode = $request->query->get('c', null);

		$params = array(
			'menu_drugs' => 'atc',
			'title'      => 'АТХ',
			'ATCCode'    => $atcCode,
			'choices'    => $choices,
		);

		return $params;
	}

	/**
	 * [AJAX] Подгрузка дерева ATC
	 *
	 * @Route("/drugs/atc-ajax", name="atc_ajax", options={"expose":true})
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
	 * @Route("/drugs/atc-generator", name="atc_generator")
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
	 * @Route("/drugs/clinic-pointer/{id}", name="kfu_item", options={"expose":true})
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
			'title'      => $this->strip($kfu) . ' | Клинико-фармакологические указатели',
			'parent'     => $em->getRepository('VidalDrugBundle:ClinicoPhPointers')->findParent($kfu),
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
	 * @Route("/drugs/clinic-pointers", name="kfu")
	 * @Template("VidalDrugBundle:Drugs:kfu.html.twig")
	 */
	public function kfuAction(Request $request)
	{
		$params = array(
			'menu_drugs' => 'kfu',
			'title'      => 'Клинико-фармакологические указатели',
		);

		if ($request->query->has('show')) {
			$em      = $this->getDoctrine()->getManager('drug');
			$show    = $request->query->get('show', null);
			$showKfu = $em->getRepository('VidalDrugBundle:ClinicoPhPointers')->findOneById($show);
			if ($showKfu) {
				$showBaseKfu = $em->getRepository('VidalDrugBundle:ClinicoPhPointers')->findBase($showKfu);
				if ($showBaseKfu) {
					$params['showKfu']     = $showKfu;
					$params['showBaseKfu'] = $showBaseKfu;
				}
			}
		}

		return $params;
	}

	/**
	 * [AJAX] Подгрузка дерева КФУ
	 *
	 * @Route("/drugs/kfu-ajax", name="kfu_ajax", options={"expose":true})
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
	 * @Route("/drugs/kfu-generator", name="kfu_generator")
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
	 * @Route("/drugs/pharm-groups", name="pharm")
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
	 * @Route("/drugs/pharm-group/{id}/{search}", name="pharm_item", defaults={"id":"\d+"})
	 * @Template("VidalDrugBundle:Drugs:pharm_item.html.twig")
	 */
	public function pharmItemAction($id, $search = 0)
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

		return $search ? $this->render('VidalDrugBundle:Drugs:search_pharm_item.html.twig', $params) : $params;
	}

	/**
	 * Список препаратов и активных веществ по показанию (Nozology)
	 *
	 * @Route("/drugs/nosology/{Code}/{search}", name="nosology_item", options={"expose":true})
	 * @Route("/poisk_preparatov/lno_{Code}", name="nosology_item_old")
	 * @Template("VidalDrugBundle:Drugs:nosology_item.html.twig")
	 */
	public function nosologyItemAction(Request $request, $Code, $search = 0)
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

		$nozology = $em->getRepository('VidalDrugBundle:Nozology')->findOneByCode($Code);

		if ($nozology === null) {
			throw $this->createNotFoundException();
		}

		$documents = $em->getRepository('VidalDrugBundle:Document')->findByNozologyCode($Code);
		$params    = array(
			'nozology' => $nozology,
			'title'    => $nozology->getName() . ' | ' . 'Нозологический указатель',
		);

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

			if (!empty($products)) {
				usort($products, function ($a, $b) {
					return strcmp($a['RusName'], $b['RusName']);
				});

				$productIds          = $this->getProductIds($products);
				$params['products']  = $products;
				$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
				$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds, date('Y'));
				$params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
			}
		}

		return $search ? $this->render('VidalDrugBundle:Drugs:search_nosology_item.html.twig', $params) : $params;
	}

	/**
	 * Список нозологических указателей
	 *
	 * @Route("/drugs/nosology", name="nosology")
	 * @Template("VidalDrugBundle:Drugs:nosology.html.twig")
	 */
	public function nosologyAction(Request $request)
	{
		$em           = $this->getDoctrine()->getManager('drug');
		$choices      = $em->getRepository('VidalDrugBundle:Nozology')->getChoices();
		$nosologyCode = $request->query->get('c', null);

		$params = array(
			'menu_drugs'   => 'nosology',
			'title'        => 'Нозологический указатель',
			'nosologyCode' => $nosologyCode,
			'choices'      => $choices,
		);

		return $params;
	}

	/**
	 * [AJAX] Подгрузка дерева Нозологических указателей
	 * @Route("/drugs/nosology-ajax", name="nosology_ajax", options={"expose":true})
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
	 * @Route("/drugs/nosology-generator", name="nosology_generator")
	 * @Template("VidalDrugBundle:Drugs:nosology_generator.html.twig")
	 * @Secure(roles="ROLE_ADMIN")
	 */
	public function nosologyGeneratorAction()
	{
		$em         = $this->getDoctrine()->getManager('drug');
		$nozologies = $em->getRepository('VidalDrugBundle:Nozology')->findForTree();

		return array('codes' => $nozologies);
	}

	/**
	 * @Route("/drugs/clinic-groups", name="clinic_groups")
	 * @Template("VidalDrugBundle:Drugs:clinic_groups.html.twig")
	 */
	public function clinicGroupsAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager('drug');
		$q  = $request->query->get('q', null);
		$l  = $request->query->get('l', null);
		$p  = $request->query->get('p', 1);

		//		$companies = $em->getRepository('VidalDrugBundle:ClPhGroups')->findWithProducts();
		//		$letters   = array();
		//		foreach ($companies as $company) {
		//			$letter = mb_strtoupper(mb_substr($company->getName(), 0, 1, 'utf-8'), 'utf-8');
		//			if (!isset($letters[$letter])) {
		//				$letters[$letter] = '';
		//			}
		//		}
		//		ksort($letters);
		//		var_dump($letters);
		//		exit;

		if ($l) {
			$query = $em->getRepository('VidalDrugBundle:ClPhGroups')->findByLetter($l);
		}
		elseif ($q) {
			$query = $em->getRepository('VidalDrugBundle:ClPhGroups')->findByQuery($q);
		}
		else {
			$query = $em->getRepository('VidalDrugBundle:ClPhGroups')->getQuery();
		}

		$params = array(
			'menu_drugs' => 'clinic_groups',
			'title'      => 'Клинико-фармакологические группы',
			'q'          => $q,
			'l'          => $l,
			'pagination' => $this->get('knp_paginator')->paginate($query, $p, self::KFG_PER_PAGE),
		);

		return $params;
	}

	/**
	 * @Route("/drugs/clinic-group/{id}/{search}", name="clinic_group")
	 * @Template("VidalDrugBundle:Drugs:clinic_group.html.twig")
	 */
	public function clinicGroupAction($id, $search = 0)
	{
		$em        = $this->getDoctrine()->getManager('drug');
		$clphGroup = $em->getRepository('VidalDrugBundle:ClPhGroups')->findOneById($id);

		if (!$clphGroup) {
			return $this->createNotFoundException();
		}

		$params = array(
			'title'     => $this->strip($clphGroup->getName()) . ' | Клинико-фармакологические группы',
			'clphGroup' => $clphGroup,
		);

		$products = $em->getRepository('VidalDrugBundle:Product')->findByClPhGroupID($id);

		if (!empty($products)) {
			$productIds          = $this->getProductIds($products);
			$params['products']  = $products;
			$params['companies'] = $em->getRepository('VidalDrugBundle:Company')->findByProducts($productIds);
			$params['pictures']  = $em->getRepository('VidalDrugBundle:Picture')->findByProductIds($productIds, date('Y'));
			$params['infoPages'] = $em->getRepository('VidalDrugBundle:InfoPage')->findByProducts($products);
		}

		return $search ? $this->render('VidalDrugBundle:Drugs:search_clinic_group.html.twig', $params) : $params;
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
